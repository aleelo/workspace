<?php

namespace App\Models;

class Purchase_Item_model extends Crud_model {

    protected $table = 'purchase_items';

    function __construct() {
        $this->table = 'purchase_items';
        parent::__construct($this->table);
    }

    
    function get_details($options = array()) {
        $items_table = $this->db->prefixTable('purchase_items');
        $purchase_order_items = $this->db->prefixTable('purchase_order_items');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $items_table.id=$id";
        }

        $search = get_array_value($options, "search");
        if ($search) {
            $search = $this->db->escapeLikeString($search);
            $where .= " AND ($items_table.name LIKE '%$search%' ESCAPE '!' OR $items_table.description LIKE '%$search%' ESCAPE '!')";
        }
      
        $extra_select = "";
        $login_user_id = $this->_get_clean_value($options, "login_user_id");
      
        $limit_query = "";
        $limit = $this->_get_clean_value($options, "limit");
        if ($limit) {
            $offset = $this->_get_clean_value($options, "offset");
            $limit_query = "LIMIT $offset, $limit";
        }

        $sql = "SELECT $items_table.* FROM $items_table
            WHERE $items_table.deleted=0 $where
            ORDER BY $items_table.name ASC
            $limit_query";

        return $this->db->query($sql);
    }
    
}
