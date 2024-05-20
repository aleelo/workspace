<?php

namespace App\Models;

class Fuel_Order_model extends Crud_model {

    protected $table = 'fuel_orders';

    function __construct() {
        $this->table = 'fuel_orders';
        parent::__construct($this->table);
    }

    
}
