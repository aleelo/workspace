<div class="tab-content">
    <?php
    $reload_url = get_uri("team_members/view/" . $user_id);
    $save_url = get_uri("team_members/save_Bank_details/" . $user_id);
    $show_submit = true;

    if (isset($user_type)) {
        if ($user_type === "client") {
            $reload_url = "";
            $save_url = get_uri("clients/save_contact_social_links/" . $user_id);
            if (isset($can_edit_clients) && !$can_edit_clients) {
                $show_submit = false;
            }
        } else if ($user_type === "lead") {
            $reload_url = "";
            $save_url = get_uri("leads/save_contact_social_links/" . $user_id);
        }
    }

    echo form_open($save_url, array("id" => "social-links-form", "class" => "general-form dashed-row white", "role" => "form"));
    ?>
    <div class="card">
        <div class=" card-header">
            <h4> <?php echo app_lang('bank_details'); ?></h4>
        </div>
        <div class="card-body">
            <div class="form-group">
                <div class="row">
                    <label for="bank_name" class=" col-md-2">Bank Name</label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "bank_name",
                            "name" => "bank_name",
                            "value" => $model_info->bank_name,
                            "class" => "form-control",
                            "placeholder" => "Bank Name"
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="bank_account" class=" col-md-2">Bank Account</label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "bank_account",
                            "name" => "bank_account",
                            "value" => $model_info->bank_account,
                            "class" => "form-control",
                            "placeholder" => "Bank Account"
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="registered_name" class=" col-md-2">Registered Name</label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "registered_name",
                            "name" => "registered_name",
                            "value" => $model_info->registered_name,
                            "class" => "form-control",
                            "placeholder" => "Registered Name"
                        ));
                        ?>
                    </div>
                </div>
            </div>
            
        </div>
        <?php if ($show_submit) { ?>
            <div class="card-footer rounded-0">
                <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
            </div>
        <?php } ?>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#social-links-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});

                var reloadUrl = "<?php echo $reload_url; ?>";
                if (reloadUrl) {
                    setTimeout(function () {
                        window.location.href = reloadUrl;
                    }, 500);
                }

            }
        });
    });
</script>    