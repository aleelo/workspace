<?php

namespace App\Models;

class Fuel_Receive_model extends Crud_model {

    protected $table = 'fuel_receives';

    function __construct() {
        $this->table = 'fuel_receives';
        parent::__construct($this->table);
    }

    
}
