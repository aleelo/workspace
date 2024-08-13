<?php

namespace App\Models;

class Visitors_model extends Crud_model {

    protected $table = 'visitors';

    function __construct() {
        $this->table = 'visitors';
        parent::__construct($this->table);
    }

    
}
