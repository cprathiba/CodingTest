<?php

namespace SpriiTestApp\model;

interface Model {

    public function create();

    public function read();

    public function update();

    public function delete();

    public function fetchAll();
}
