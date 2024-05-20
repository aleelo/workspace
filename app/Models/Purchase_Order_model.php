<?php

namespace App\Models;

class Purchase_Order_model extends Crud_model {

    protected $table = 'purchase_orders';

    function __construct() {
        $this->table = 'purchase_orders';
        parent::__construct($this->table);
    }

    
    function get_details($options = array()) {
        $purchase_order_table = $this->db->prefixTable('purchase_orders');
        $users_table = $this->db->prefixTable('users');
        $suppliers_table = $this->db->prefixTable('suppliers');
        $departments_table = 'departments';
        
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $purchase_order_table.id=$id";
        }
        

        $sql = "SELECT $purchase_order_table.*,$suppliers_table.supplier_name as supplier,$users_table.image as avatar,$departments_table.nameSo as department,
        concat($users_table.first_name,' ',$users_table.last_name) user
         FROM $purchase_order_table
        LEFT JOIN $users_table ON $purchase_order_table.ordered_by=$users_table.id
        LEFT JOIN $suppliers_table ON $purchase_order_table.supplier_id=$suppliers_table.id
        LEFT JOIN $departments_table ON $purchase_order_table.department_id=$departments_table.id
        WHERE $purchase_order_table.deleted=0 $where
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

   
    function get_purchase_total_summary($purchase_id) {
        $invoice_payments_table = $this->db->prefixTable('invoice_payments');
        $clients_table = $this->db->prefixTable('clients');
        $invoices_table = $this->db->prefixTable('invoices');

        $result = $this->get_invoice_total_meta($purchase_id);

        $client_sql = "SELECT $clients_table.currency_symbol, $clients_table.currency FROM $clients_table WHERE $clients_table.id=(SELECT $invoices_table.client_id FROM $invoices_table WHERE $invoices_table.id=$invoice_id LIMIT 1)";
        $client = $this->db->query($client_sql)->getRow();

        $result->currency_symbol = $client->currency_symbol ? $client->currency_symbol : get_setting("currency_symbol");
        $result->currency = $client->currency ? $client->currency : get_setting("default_currency");

        $payment_sql = "SELECT SUM($invoice_payments_table.amount) AS total_paid
        FROM $invoice_payments_table
        WHERE $invoice_payments_table.deleted=0 AND $invoice_payments_table.invoice_id=$invoice_id";
        $payment = $this->db->query($payment_sql)->getRow();

        $result->total_paid = is_null($payment->total_paid) ? 0 : $payment->total_paid;
        $result->balance_due = number_format($result->invoice_total, 2, ".", "") - number_format($result->total_paid, 2, ".", "");

        return $result;
    }
 
}
