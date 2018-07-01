<?php

namespace SpriiTestApp\model;

use SpriiTestApp\dba\DBA;

abstract class Entity implements Model {

    protected $table;
    protected $fields = array();
    protected $values = array();
    protected $hasMany = array();
    protected $primaryKey = 'id';
    protected $labelProperty = 'name';

    public function getTable() {
        return $this->table;
    }

    public function getPrimaryKey() {
        return $this->primaryKey;
    }

    public function getLabelProperty() {
        return $this->labelProperty;
    }

    public function create() {

        $fields = $this->fields;
        unset($fields[$this->primaryKey]);

        $fieldList = implode('`,`', $fields);

        $valuePlacehoders = array();
        $params = array();
        array_walk($fields, function(&$item, $key) use (&$valuePlacehoders, &$params) {
            $placeholder = ":{$key}";
            $valuePlacehoders[] = $placeholder;
            $params[$placeholder] = $this->values[$key];
        });

        $valuePlacehoderList = implode(', ', $valuePlacehoders);
        $sql = "INSERT INTO {$this->table} (`$fieldList`) VALUES ({$valuePlacehoderList});";

        DBA::instance()->execute($sql, $params);

        list($id) = DBA::instance()->fetchColumn('SELECT LAST_INSERT_ID();');

        foreach ($this->hasMany as $relation => $relationDef) {
            $relationSql = "INSERT INTO `{$relationDef['table']}` VALUES (?, ?)";
            foreach ($relationDef['refKeyValues'] as $refKeyValue) {
                var_dump($relationSql, $id, $refKeyValue);
                DBA::instance()->execute($relationSql, array($id, $refKeyValue));
            }
        }
    }

    public function delete() {
        if (empty($this->values[$this->primaryKey])) {
            throw new \Exception('Primary key value is not set');
        }

        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?;";
        try {
        DBA::instance()->execute($sql, array($this->values[$this->primaryKey]));
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function read() {
        if (empty($this->values[$this->primaryKey])) {
            throw new \Exception('Primary key value is not set');
        }

        $selectFields = array();
        array_walk($this->fields, function(&$item, $key) use (&$selectFields) {
            $selectFields[] = "`{$item}` AS `{$key}`";
        });
        $fieldList = implode(',', $selectFields);
        $sql = "SELECT {$fieldList} FROM {$this->table} WHERE `{$this->primaryKey}` = ?;";

        return DBA::instance()->fetchObject($sql, $this->values[$this->primaryKey], get_class($this));
    }

    public function update() {
        $fields = $this->fields;
        unset($fields[$this->primaryKey]);

        $updatePlaceholders = array();
        $params = array();
        array_walk($fields, function(&$item, $key) use (&$updatePlaceholders, &$params) {
            $updatePlaceholders[] = "{$item} = :{$key}";
            $params[":{$key}"] = $this->values[$key];
        });
        $params['pkValue'] = $this->values[$this->primaryKey];

        $valuePlacehoderList = implode(', ', $updatePlaceholders);
        $sql = "UPDATE {$this->table} SET {$valuePlacehoderList} WHERE {$this->primaryKey} = :pkValue;";
        DBA::instance()->execute($sql, $params);

        foreach ($this->hasMany as $relation => $relationDef) {
            $deleteSql = "DELETE FROM `{$relationDef['table']}` WHERE `{$relationDef['foreignKey']}` = ? ;";
            DBA::instance()->execute($deleteSql, array($this->values[$this->primaryKey]));

            $relationSql = "INSERT INTO `{$relationDef['table']}` VALUES (?, ?)";
            foreach ($relationDef['refKeyValues'] as $refKeyValue) {
                DBA::instance()->execute($relationSql, array($this->values[$this->primaryKey], $refKeyValue));
            }
        }
    }

    public function save() {
        if (empty($this->values[$this->primaryKey])) {
            $this->create();
        } else {
            $this->update();
        }
    }

    public function fetchAll() {
        $selectFields = array();
        array_walk($this->fields, function(&$item, $key) use (&$selectFields) {
            $selectFields[] = "`{$item}` AS `{$key}`";
        });
        $fieldList = implode(',', $selectFields);
        $sql = "SELECT {$fieldList} FROM {$this->table};";
        return DBA::instance()->fetchAll($sql, get_class($this));
    }

    public function __get($name) {
        if (array_key_exists($name, $this->fields)) {
            return isset($this->values[$name]) ? $this->values[$name] : null;
        } elseif (array_key_exists($name, $this->hasMany)) {

            $refClass = new $this->hasMany[$name]['refClass'];
            $sql = "SELECT `{$refClass->getLabelProperty()}` AS `labelProperty` "
                    . "FROM `{$refClass->getTable()}` a "
                    . "LEFT JOIN `{$this->hasMany[$name]['table']}` b ON a.`{$refClass->getPrimaryKey()}` = b.`{$this->hasMany[$name]['refKey']}` "
                    . "WHERE b.`{$this->hasMany[$name]['foreignKey']}` = ?";
            $results = DBA::instance()->fetchColumn($sql, array($this->values[$this->primaryKey]));

            $this->values[$name] = implode(', ', $results);

            return $this->values[$name];
        } else {
            // TODO: Warn
        }
    }

    public function __set($name, $value) {
        if (array_key_exists($name, $this->fields)) {
            $this->values[$name] = $value;
        } elseif (array_key_exists($name, $this->hasMany)) {
            $this->hasMany[$name]['refKeyValues'] = $value;
        } else {
            // TODO: Warn
        }
    }

}
