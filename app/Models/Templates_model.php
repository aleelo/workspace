<?php

namespace App\Models;

class Templates_model extends Crud_model {

    protected $table = 'templates';

    function __construct() {
        
        parent::__construct($this->table);

    }

    
}
