<?php

namespace App\Models;

class TravelDetails_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'travel_details';
        parent::__construct($this->table);
    }
    function get_details($options = array()) {
        // Define the necessary table names
        $travel_details_table = $this->db->prefixTable('travel_details');
        $passenger_table = $this->db->prefixTable('passenger_details');
        $countries_table = $this->db->prefixTable('countries');
    
        // Initialize the WHERE clause
        $where = "";
    
        // If a specific ID is passed, filter by it
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $travel_details_table.id=$id";
        }
    
        // Additional conditions can go here
        // Add more filters like ref_number, created_by, etc. as needed
    
        // Set up pagination if needed
        $limit_offset = "";
        $limit = $this->_get_clean_value($options, "limit");
        if ($limit) {
            $skip = $this->_get_clean_value($options, "skip");
            $offset = $skip ? $skip : 0;
            $limit_offset = " LIMIT $limit OFFSET $offset ";
        }
    
        // Set up ordering if required
        $order = "";
        $available_order_by_list = array(
            "id" => "$travel_details_table.id",
            "fullName" => "CONCAT(p.first_name, ' ', p.middle_name, ' ', p.last_name)",
            "departure_country" => "dpc.name",
            "destination_country" => "dcc.name",
            "transit_country" => "tcc.name"
        );
        $order_by = get_array_value($available_order_by_list, $this->_get_clean_value($options, "order_by"));
        if ($order_by) {
            $order_dir = $this->_get_clean_value($options, "order_dir", "ASC");
            $order = " ORDER BY $order_by $order_dir ";
        }
    
        // Prepare the SQL query
        $sql = "SELECT SQL_CALC_FOUND_ROWS t.*, 
                    CONCAT(p.first_name, ' ', p.middle_name, ' ', p.last_name) as fullName, 
                    dpc.name as departure_country, 
                    dcc.name as destination_country, 
                    tcc.name as transit_country
                FROM $travel_details_table t
                LEFT JOIN $passenger_table p ON p.id = t.passenger_id
                LEFT JOIN $countries_table dpc ON dpc.id = t.departure_country_id
                LEFT JOIN $countries_table dcc ON dcc.id = t.destination_country_id
                LEFT JOIN $countries_table tcc ON tcc.id = t.transit_country_id
                WHERE t.deleted = 0 $where 
                $order $limit_offset";
    
        // Execute the query
        $raw_query = $this->db->query($sql);
    
        // Get the total number of rows
        $total_rows = $this->db->query("SELECT FOUND_ROWS() as found_rows")->getRow();
    
        // Return the data
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
