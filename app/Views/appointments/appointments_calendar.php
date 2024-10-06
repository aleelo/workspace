<?php
load_css(array(
    "assets/js/fullcalendar/fullcalendar.min.css"
));

load_js(array(
    "assets/js/fullcalendar/fullcalendar.min.js",
    "assets/js/fullcalendar/locales-all.min.js"
));

$client = "";
if (isset($client_id)) {
    $client = $client_id;
}
?>
    <div class="card mb0 full-width-button">
        <div class="page-title clearfix">
            <div class="title-button-group custom-toolbar events-title-button">


                <?php if ($calendar_filter_dropdown) { ?>
                    <div id="calendar-filter-dropdown" class="float-start <?php echo (count($calendar_filter_dropdown) == 1) ? "hide" : ""; ?>" style="display: none;"></div>
                <?php } ?>


                <?php
                if (get_setting("enable_google_calendar_api") && (get_setting("google_calendar_authorized") || get_setting('user_' . $login_user->id . '_google_calendar_authorized'))) {
                    echo modal_anchor(get_uri("events/google_calendar_settings_modal_form"), "<i data-feather='settings' class='icon-16'></i> " . app_lang('google_calendar_settings'), array("class" => "btn btn-default", "title" => app_lang('google_calendar_settings')));
                }
                ?>

                <!-- <?php //echo modal_anchor(get_uri("appointments/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_event'), array("class" => "btn btn-default add-btn", "title" => app_lang('add_event'), "data-post-client_id" => $client)); ?> -->

                <?php echo modal_anchor(get_uri("appointments/modal_form"), "", array("class" => "hide", "id" => "add_event_hidden", "title" => app_lang('add_appointment'))); ?>
                <?php echo modal_anchor(get_uri("appointments/appointments_view"), "", array("class" => "hide", "id" => "show_event_hidden", "data-post-client_id" => $client, "data-post-cycle" => "0", "data-post-editable" => "1", "title" => app_lang('appointment_details'))); ?>
            </div>
        </div>
        <div class="card-body">
            <div id="event-calendar"></div>
        </div>
    </div>

<script type="text/javascript">
    var filterValues = "",
            eventLabel = "";

    var loadCalendar = function () {
        var filter_values = filterValues || "events",
                $eventCalendar = document.getElementById('event-calendar'),
                event_label = eventLabel || "0";

        appLoader.show();

        window.fullCalendar = new FullCalendar.Calendar($eventCalendar, {
            locale: AppLanugage.locale,
            height: $(window).height() - 210,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            },
            events: "<?php echo_uri("appointments/calendar_appointments/"); ?>" + filter_values + "/" + event_label + "/" + "<?php echo "/$client"; ?>",
            dayMaxEvents: false,
            dateClick: function (date, jsEvent, view) {
                $("#add_event_hidden").attr("data-post-appointment_date", moment(date.date).format("YYYY-MM-DD"));
                var startTime = moment(date.date).format("HH:mm:ss");
               
                $("#add_event_hidden").trigger("click");

               setTimeout(function(){
                $('#appointment_date').val(moment(date.date).format("YYYY-MM-DD"));
               },1000);
               
            },
            eventClick: function (calEvent) {
                calEvent = calEvent.event.extendedProps;
                if (calEvent.event_type === "appointment") {
                    $("#show_event_hidden").attr("data-post-id", calEvent.encrypted_appointment_id);
                    // $("#show_event_hidden").attr("data-post-cycle", calEvent.cycle);
                    $("#show_event_hidden").trigger("click");

                } else if (calEvent.event_type === "leave") {
                    $("#show_leave_hidden").attr("data-post-id", calEvent.leave_id);
                    $("#show_leave_hidden").trigger("click");

                } else if (calEvent.event_type === "project_deadline" || calEvent.event_type === "project_start_date") {
                    window.location = "<?php echo site_url('projects/view'); ?>/" + calEvent.project_id;
                } else if (calEvent.event_type === "task_deadline" || calEvent.event_type === "task_start_date") {

                    $("#show_task_hidden").attr("data-post-id", calEvent.task_id);
                    $("#show_task_hidden").trigger("click");
                }
            },
            eventContent: function (element) {
                var icon = element.event.extendedProps.icon;
                var title = element.event.title;
                if (icon) {
                    title = "<span class='clickable p5 w100p inline-block' style='background-color: " + element.event.backgroundColor + "; color: #fff'><span><i data-feather='" + icon + "' class='icon-16'></i> " + title + "</span></span>";
                }

                return {
                    html: title
                };
            },
            loading: function (state) {
                if (state === false) {
                    appLoader.hide();
                    setTimeout(function () {
                        feather.replace();
                    }, 100);
                }
            },
            firstDay: AppHelper.settings.firstDayOfWeek
        });

        window.fullCalendar.render();
    };

    $(document).ready(function () {
        $("#calendar-filter-dropdown").appMultiSelect({
            text: "<?php echo app_lang('event_type'); ?>",
            options: <?php echo json_encode($calendar_filter_dropdown); ?>,
            onChange: function (values) {
                filterValues = values.join('-');
                loadCalendar();
                setCookie("calendar_filters_of_user_<?php echo $login_user->id; ?>", values.join('-')); //save filters on browser cookie
            },
            onInit: function (values) {
                filterValues = values.join('-');
                loadCalendar();
            }
        });

        var client = "<?php echo $client; ?>";
        if (client) {
            setTimeout(function () {
                window.fullCalendar.today();
            });
        }

        //autoload the event popover
        var encrypted_event_id = "<?php echo isset($encrypted_event_id) ? $encrypted_event_id : ''; ?>";
        if (encrypted_event_id) {
            $("#show_event_hidden").attr("data-post-id", encrypted_event_id);
            $("#show_event_hidden").trigger("click");
        }

        $("#event-labels-dropdown").select2({
            data: <?php echo $event_labels_dropdown; ?>
        }).on("change", function () {
            eventLabel = $(this).val();
            loadCalendar();
        });

        $("#event-calendar .fc-header-toolbar .fc-button").click(function () {
            feather.replace();
        });
    });
</script>