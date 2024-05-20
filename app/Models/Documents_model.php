<?php

namespace App\Models;

class Documents_model extends Crud_model {

    protected $table = 'documents';

    function __construct() {
        $this->table = 'documents';
        parent::__construct($this->table);
    }

    
}
