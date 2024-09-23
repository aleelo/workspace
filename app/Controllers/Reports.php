<?php

namespace App\Controllers;

class Reports extends Security_Controller {

    function __construct() {
        parent::__construct();
        // $this->access_only_team_members();
        $this->init_permission_checker("report");

    }

    public function index() {
        $this->check_module_availability("module_report");
        $this->access_only_allowed_members();
        $redirect_to = "";
        $reports = get_reports_topbar(true);
        $count = 1;
        foreach ($reports as $report) {
            if ($count == 1) {
                if (get_array_value($report, "single_button") == 1) {
                    $redirect_to = get_array_value($report, "url");
                } else {
                    foreach (get_array_value($report, "dropdown_item") as $sub_page) {
                        if ($count == 1) {
                            $redirect_to = get_array_value($sub_page, "url");
                        }
                        $count++;
                    }
                }
            } else {
                continue;
            }
            $count++;
        }

        $view_data["redirect_to"] = $redirect_to;
        return $this->template->rander("reports/index", $view_data);
    }
}

/* End of file Reports.php */
/* Location: ./app/controllers/Reports.php */