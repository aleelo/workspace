<?php

namespace App\Models;

class Purchase_Order_Items_model extends Crud_model {

    protected $table = 'rise_purchase_order_items';

    function __construct() {
        $this->table = 'rise_purchase_order_items';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $purchase_order_table = $this->db->prefixTable('purchase_orders');
        $users_table = $this->db->prefixTable('users');
        $purchase_items_table = $this->db->prefixTable('purchase_items');
        $purchase_order_items_table = $this->db->prefixTable('purchase_order_items');
        
        $where = "";
        $purchase_order_id = $this->_get_clean_value($options, "purchase_order_id");
        if ($purchase_order_id) {
            $where .= " AND $purchase_order_items_table.purchase_order_id=$purchase_order_id";
        }

        $item_id = $this->_get_clean_value($options, "item_id");
        if ($item_id) {
            $where .= " AND $purchase_order_items_table.item_id=$item_id";
        }
        

        $sql = "SELECT $purchase_order_items_table.* FROM $purchase_order_items_table
                LEFT JOIN $purchase_items_table ON $purchase_items_table.id=$purchase_order_items_table.item_id
                LEFT JOIN $purchase_order_table ON $purchase_order_items_table.purchase_order_id=$purchase_order_table.id
                WHERE $purchase_order_items_table.deleted=0 $where
                ORDER BY $purchase_order_table.id DESC";
        
        return $this->db->query($sql);
    }
    
    function get_order_items($options = array()) {
        $purchase_order_table = $this->db->prefixTable('purchase_orders');
        $users_table = $this->db->prefixTable('users');
        $purchase_items_table = $this->db->prefixTable('purchase_items');
        $purchase_order_items_table = $this->db->prefixTable('purchase_order_items');
        
        $where = "";
        $purchase_order_id = $this->_get_clean_value($options, "purchase_order_id");
        if ($purchase_order_id) {
            $where .= " AND $purchase_order_items_table.purchase_order_id=$purchase_order_id";
        }

        $item_id = $this->_get_clean_value($options, "item_id");
        if ($item_id) {
            $where .= " AND $purchase_order_items_table.item_id=$item_id";
        }
        

        $sql = "SELECT $purchase_order_items_table.* FROM $purchase_order_items_table
                LEFT JOIN $purchase_items_table ON $purchase_items_table.id=$purchase_order_items_table.item_id
                LEFT JOIN $purchase_order_table ON $purchase_order_items_table.purchase_order_id=$purchase_order_table.id
                WHERE $purchase_order_items_table.deleted=0 $where
                ORDER BY $purchase_order_table.id DESC";
        
        return $this->db->query($sql);
    }
    
    function get_item_suggestion($keyword = "", $user_type = "") {
        $items_table = $this->db->prefixTable('purchase_items');

        if ($keyword) {
            $keyword = $this->db->escapeLikeString($keyword);
        }
       
        $sql = "SELECT $items_table.id, $items_table.name
        FROM $items_table
        WHERE $items_table.deleted=0  AND $items_table.name LIKE '%$keyword%' ESCAPE '!' 
        LIMIT 10 
        ";
        return $this->db->query($sql)->getResult();
    }

    function get_item_info_suggestion($options = array()) {

        $items_table = $this->db->prefixTable('purchase_items');

        $where = "";
        $item_name = get_array_value($options, "item_name");
        if ($item_name) {
            $item_name = $this->db->escapeLikeString($item_name);
            $where .= " AND $items_table.name LIKE '%$item_name%' ESCAPE '!'";
        }

        $item_id = $this->_get_clean_value($options, "item_id");
        if ($item_id) {
            $where .= " AND $items_table.id=$item_id";
        }    

        $sql = "SELECT $items_table.*
        FROM $items_table
        WHERE $items_table.deleted=0 $where
        ORDER BY id DESC LIMIT 1
        ";

        $result = $this->db->query($sql);

        if ($result->resultID->num_rows) {
            return $result->getRow();
        }
    }

    function save_item_and_update_invoice($data, $id, $invoice_id) {
        $result = $this->ci_save($data, $id);

        $invoices_model = model("App\Models\Invoices_model");
        $invoices_model->update_invoice_total_meta($invoice_id);

        return $result;
    }

    function delete_item_and_update_invoice($id, $undo = false) {
        $item_info = $this->get_one($id);

        $result = $this->delete($id, $undo);

        $invoices_model = model("App\Models\Invoices_model");
        $invoices_model->update_invoice_total_meta($item_info->invoice_id);

        return $result;
    }
    
}
