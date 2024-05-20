<?php

namespace App\Models;

class Fuel_Request_model extends Crud_model {

    protected $table = 'fuel_requests';

    function __construct() {
        $this->table = 'fuel_requests';
        parent::__construct($this->table);
    }

    
}
