<?php

namespace App\Models;

class Purchase_Receive_model extends Crud_model {

    protected $table = 'purchase_receives';

    function __construct() {
        $this->table = 'purchase_receives';
        parent::__construct($this->table);
    }

    
}
