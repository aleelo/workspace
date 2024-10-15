<?php

namespace App\Models;

class PassengerDetails_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'passenger_details';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $passenger_details_table = $this->db->prefixTable('passenger_details');
        $departments_table = $this->db->prefixTable('departments');
        $passenger_table=$this->db->prefixTable('passenger_details');
      
        $countries_table=$this->db->prefixTable('countries');
        $projects_table = $this->db->prefixTable('projects');
        $users_table = $this->db->prefixTable('users');
        $invoices_table = $this->db->prefixTable('invoices');
        $invoice_payments_table = $this->db->prefixTable('invoice_payments');
        $client_groups_table = $this->db->prefixTable('client_groups');
        $lead_status_table = $this->db->prefixTable('lead_status');
        $estimates_table = $this->db->prefixTable('estimates');
        $estimate_requests_table = $this->db->prefixTable('estimate_requests');
        $tickets_table = $this->db->prefixTable('tickets');
        $orders_table = $this->db->prefixTable('orders');
        $proposals_table = $this->db->prefixTable('proposals');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $passenger_details_table.id=$id";
        }

        $ref_number = $this->_get_clean_value($options, "ref_number");
        if ($ref_number) {
            $where .= " AND $passenger_details_table.ref_number=$ref_number";
        }


        $created_by = $this->_get_clean_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $passenger_details_table.created_by=$created_by";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');

        $limit_offset = "";
        $limit = $this->_get_clean_value($options, "limit");
        if ($limit) {
            $skip = $this->_get_clean_value($options, "skip");
            $offset = $skip ? $skip : 0;
            $limit_offset = " LIMIT $limit OFFSET $offset ";
        }
        
         
        $available_order_by_list = array(
            "id" => $passenger_details_table . ".id",
            "name" => $passenger_details_table . ".name",
            "ref_number" => $passenger_details_table . ".ref_number",
            "fullName" => "CONCAT($users_table.first_name, ' ', $users_table.last_name)",
            "trip_type" => $passenger_details_table . ".trip_type",
        );

        $order_by = get_array_value($available_order_by_list, $this->_get_clean_value($options, "order_by"));

        $order = "";

        if ($order_by) {
            $order_dir = $this->_get_clean_value($options, "order_dir");
            $order = " ORDER BY $order_by $order_dir ";
        }


        $search_by = get_array_value($options, "search_by");
        if ($search_by) {
            $search_by = $this->db->escapeLikeString($search_by);
            $labels_table = $this->db->prefixTable("labels");

            $where .= " AND (";
            $where .= " $passenger_details_table.id LIKE '%$search_by%' ESCAPE '!' ";
            $where .= " OR $passenger_details_table.passport_no LIKE '%$search_by%' ESCAPE '!' ";
            $where .= " OR $passenger_details_table.gender LIKE '%$search_by%' ESCAPE '!' ";
            $where .= " OR $passenger_details_table.city LIKE '%$search_by%' ESCAPE '!' ";
             
            $where .= " )";
        }


        $sql = "SELECT SQL_CALC_FOUND_ROWS $passenger_details_table.*,
        CONCAT($passenger_table.first_name,' ',$passenger_table.middle_name,' ',$passenger_table.last_name) as fullName,$countries_table.name
        FROM $passenger_details_table
           LEFT JOIN $countries_table on $countries_table.id=$passenger_table.country_id
                
        WHERE $passenger_details_table.deleted=0 $where    
        $order $limit_offset";

        // print_r($sql);die;

        $raw_query = $this->db->query($sql);

        $total_rows = $this->db->query("SELECT FOUND_ROWS() as found_rows")->getRow();

        if ($limit) {
            return array(
                "data" => $raw_query->getResult(),
                "recordsTotal" => $total_rows->found_rows,
                "recordsFiltered" => $total_rows->found_rows,
            );
        } else {
            return $raw_query;
        }
    }
 

}
