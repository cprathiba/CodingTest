<?php

namespace SpriiTestApp\model;

class Student extends Entity {

    protected $table = 'student';
    protected $fields = array(
        'id' => 'id',
        'firstName' => 'first_name',
        'lastName' => 'last_name',
    );
    protected $hasMany = array(
        'subjects' => array(
            'table' => 'student_subject',
            'foreignKey' => 'student_id',
            'refClass' => 'SpriiTestApp\model\Subject',
            'refKey' => 'subject_id',
            'refKeyValues' => array(),
        ),
    );

}
