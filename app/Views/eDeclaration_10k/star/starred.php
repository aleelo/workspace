<?php

echo ajax_anchor(get_uri("edeclaration_10k/add_remove_star/" . $client_id . "/remove"), "<i data-feather='star' class='icon-16 icon-fill-warning'></i>", array("data-real-target" => "#star-mark", "class" => "star-icon"));
