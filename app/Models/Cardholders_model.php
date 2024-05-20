<?php

namespace App\Models;

class Cardholders_model extends Crud_model {

    protected $table = 'cardholders';

    function __construct() {
        $this->table = 'cardholders';
        parent::__construct($this->table);
    }

    
}
