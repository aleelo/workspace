<?php

namespace App\Models;

class eDeclaration_10k_Model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'materials';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $materials_table = $this->db->prefixTable('materials');
        $departments_table = $this->db->prefixTable('departments');
        $passenger_table=$this->db->prefixTable('passenger_details');
      
        $travel_table=$this->db->prefixTable('travel_details');
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
        
        $countries_table = $this->db->prefixTable('countries');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $materials_table.id=$id";
        }

        $ref_number = $this->_get_clean_value($options, "ref_number");
        if ($ref_number) {
            $where .= " AND $materials_table.ref_number=$ref_number";
        }

        $q_type = $this->_get_clean_value($options, "q_type");
        if ($q_type) {
            $where .= " AND $materials_table.q_type='$q_type'";
        }

        $created_by = $this->_get_clean_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $materials_table.created_by=$created_by";
        }


        $trip_type = $this->_get_clean_value($options, "trip_type");
        
        if($trip_type == "Arrival_list"){
            $where .= " AND $materials_table.trip_type = 'Arrival'";
        }elseif($trip_type == "Depature_list"){
            $where .= " AND $materials_table.trip_type = 'Departure'";
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
            "id" => $materials_table . ".id",
            "name" => $materials_table . ".name",
            "ref_number" => $materials_table . ".ref_number",
            "fullName" => "CONCAT($users_table.first_name, ' ', $users_table.last_name)",
            "trip_type" => $materials_table . ".trip_type",
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
            $where .= " $materials_table.id LIKE '%$search_by%' ESCAPE '!' ";
            $where .= " OR $materials_table.name LIKE '%$search_by%' ESCAPE '!' ";
            $where .= " OR $materials_table.trip_type LIKE '%$search_by%' ESCAPE '!' ";
            $where .= " OR $materials_table.ref_number LIKE '%$search_by%' ESCAPE '!' ";
            $where .= " OR CONCAT($users_table.first_name, ' ', $users_table.last_name) LIKE '%$search_by%' ESCAPE '!' ";
            
            $where .= " )";
        }


        $sql = "SELECT SQL_CALC_FOUND_ROWS $materials_table.*,
        CONCAT($passenger_table.first_name,' ',$passenger_table.middle_name,' ',$passenger_table.last_name) as fullName,$passenger_table.status,
        $travel_table.travel_type, $travel_table.departure_date, $travel_table.arrival_date, dpc.name as departure_country,
         dcc.name as destination_country, tcc.name as transit_country
        FROM $materials_table
        LEFT JOIN $passenger_table ON $passenger_table.id = $materials_table.passenger_id
        LEFT JOIN $travel_table on $travel_table.passenger_id=$passenger_table.id
        LEFT JOIN $countries_table dpc ON dpc.id = $travel_table.departure_country_id
        LEFT JOIN $countries_table dcc ON dcc.id = $travel_table.destination_country_id
        LEFT JOIN $countries_table tcc ON tcc.id = $travel_table.transit_country_id
                 
        WHERE $materials_table.deleted=0 $where    
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

    private function make_quick_filter_query($filter, $clients_table, $projects_table, $invoices_table, $invoice_payments_table, $estimates_table, $estimate_requests_table, $tickets_table, $orders_table, $proposals_table) {
        $query = "";
        $tolarance = get_paid_status_tolarance();
        if ($filter == "has_open_projects" || $filter == "has_completed_projects" || $filter == "has_any_hold_projects" || $filter == "has_canceled_projects") {
            $status_id = 1;
            if ($filter == "has_completed_projects") {
                $status_id = 2;
            } else if ($filter == "has_any_hold_projects") {
                $status_id = 3;
            } else if ($filter == "has_canceled_projects") {
                $status_id = 4;
            }

            $query = " AND $clients_table.id IN(SELECT $projects_table.client_id FROM $projects_table WHERE $projects_table.deleted=0 AND $projects_table.project_type='client_project' AND $projects_table.status_id='$status_id') ";
        } else if ($filter == "has_unpaid_invoices" || $filter == "has_overdue_invoices" || $filter == "has_partially_paid_invoices") {
            $now = get_my_local_time("Y-m-d");

            $invoice_where = " AND $invoices_table.status ='not_paid' AND IFNULL(payments_table.payment_received,0)<=0"; //has_unpaid_invoices
            if ($filter == "has_overdue_invoices") {
                $invoice_where = " AND $invoices_table.status ='not_paid' AND $invoices_table.due_date<'$now' AND TRUNCATE(IFNULL(payments_table.payment_received,0),2)<$invoices_table.invoice_total-$tolarance";
            } else if ($filter == "has_partially_paid_invoices") {
                $invoice_where = " AND IFNULL(payments_table.payment_received,0)>0 AND IFNULL(payments_table.payment_received,0)<$invoices_table.invoice_total-$tolarance";
            }

            $query = " AND $clients_table.id IN(
                            SELECT $invoices_table.client_id FROM $invoices_table 
                               LEFT JOIN (SELECT invoice_id, SUM(amount) AS payment_received FROM $invoice_payments_table WHERE deleted=0 GROUP BY invoice_id) AS payments_table ON payments_table.invoice_id = $invoices_table.id  
                            WHERE $invoices_table.deleted=0 $invoice_where
                    ) ";
        } else if ($filter == "has_open_estimates" || $filter == "has_accepted_estimates") {
            $status = "sent";
            if ($filter == "has_accepted_estimates") {
                $status = "accepted";
            }

            $query = " AND $clients_table.id IN(SELECT $estimates_table.client_id FROM $estimates_table WHERE $estimates_table.deleted=0 AND $estimates_table.status='$status') ";
        } else if ($filter == "has_new_estimate_requests" || $filter == "has_estimate_requests_in_progress") {
            $status = "new";
            if ($filter == "has_estimate_requests_in_progress") {
                $status = "processing";
            }

            $query = " AND $clients_table.id IN(SELECT $estimate_requests_table.client_id FROM $estimate_requests_table WHERE $estimate_requests_table.deleted=0 AND $estimate_requests_table.status='$status') ";
        } else if ($filter == "has_open_tickets") {
            $query = " AND $clients_table.id IN(SELECT $tickets_table.client_id FROM $tickets_table WHERE $tickets_table.deleted=0 AND $tickets_table.status!='closed') ";
        } else if ($filter == "has_new_orders") {
            $query = " AND $clients_table.id IN(SELECT $orders_table.client_id FROM $orders_table WHERE $orders_table.deleted=0 AND $orders_table.status_id='1') ";
        } else if ($filter == "has_open_proposals" || $filter == "has_accepted_proposals" || $filter == "has_rejected_proposals") {
            $status = "sent";
            if ($filter == "has_accepted_proposals") {
                $status = "accepted";
            } else if ($filter == "has_rejected_proposals") {
                $status = "declined";
            }

            $query = " AND $clients_table.id IN(SELECT $proposals_table.client_id FROM $proposals_table WHERE $proposals_table.deleted=0 AND $proposals_table.status='$status') ";
        }

        return $query;
    }

    function get_primary_contact($client_id = 0, $info = false) {
        $users_table = $this->db->prefixTable('users');

        $sql = "SELECT $users_table.id, $users_table.first_name, $users_table.last_name
        FROM $users_table
        WHERE $users_table.deleted=0 AND $users_table.client_id=$client_id AND $users_table.is_primary_contact=1";
        $result = $this->db->query($sql);
        if ($result->resultID->num_rows) {
            if ($info) {
                return $result->getRow();
            } else {
                return $result->getRow()->id;
            }
        }
    }

    function add_remove_star($client_id, $user_id, $type = "add") {
        $clients_table = $this->db->prefixTable('clients');
        $client_id = $client_id ? $this->db->escapeString($client_id) : $client_id;

        $action = " CONCAT($clients_table.starred_by,',',':$user_id:') ";
        $where = " AND FIND_IN_SET(':$user_id:',$clients_table.starred_by) = 0"; //don't add duplicate

        if ($type != "add") {
            $action = " REPLACE($clients_table.starred_by, ',:$user_id:', '') ";
            $where = "";
        }

        $sql = "UPDATE $clients_table SET $clients_table.starred_by = $action
        WHERE $clients_table.id=$client_id $where";
        return $this->db->query($sql);
    }

    function get_starred_clients($user_id, $client_groups = "") {
        $clients_table = $this->db->prefixTable('clients');

        $where = $this->prepare_allowed_client_groups_query($clients_table, $client_groups);

        $sql = "SELECT $clients_table.id,  $clients_table.company_name
        FROM $clients_table
        WHERE $clients_table.deleted=0 AND FIND_IN_SET(':$user_id:',$clients_table.starred_by) $where
        ORDER BY $clients_table.company_name ASC";
        return $this->db->query($sql);
    }

    function delete_client_and_sub_items($client_id) {
        $clients_table = $this->db->prefixTable('clients');
        $general_files_table = $this->db->prefixTable('general_files');
        $users_table = $this->db->prefixTable('users');

        //get client files info to delete the files from directory 
        $client_files_sql = "SELECT * FROM $general_files_table WHERE $general_files_table.deleted=0 AND $general_files_table.client_id=$client_id; ";
        $client_files = $this->db->query($client_files_sql)->getResult();

        //delete the client and sub items
        //delete client
        $delete_client_sql = "UPDATE $clients_table SET $clients_table.deleted=1 WHERE $clients_table.id=$client_id; ";
        $this->db->query($delete_client_sql);

        //delete contacts
        $delete_contacts_sql = "UPDATE $users_table SET $users_table.deleted=1 WHERE $users_table.client_id=$client_id; ";
        $this->db->query($delete_contacts_sql);

        //delete the project files from directory
        $file_path = get_general_file_path("client", $client_id);
        foreach ($client_files as $file) {
            delete_app_files($file_path, array(make_array_of_file($file)));
        }

        return true;
    }

    function is_duplicate_company_name($company_name, $id = 0) {

        $result = $this->get_all_where(array("company_name" => $company_name, "is_lead" => 0, "deleted" => 0));
        if (count($result->getResult()) && $result->getRow()->id != $id) {
            return $result->getRow();
        } else {
            return false;
        }
    }



}
