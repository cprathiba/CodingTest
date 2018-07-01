<?php

namespace SpriiTestApp\model;

class Subject extends Entity {
    
    protected $table = 'subject';
    protected $fields = array(
      'id' => 'id',  
      'name' => 'name',  
    );
}
