<?php
/**
 * Created by PhpStorm.
 * User: sidsavara
 * Date: 9/22/16
 * Time: 4:26 PM
 */

const GENERATE_SLOTS = 'generateSlots';


const SLOT_START_DATE_VAR = 'slot_start_date';
const SLOT_END_DATE_VAR = 'slot_end_date';

const slot_length_var = 'slot_length';
const slot_count_var = 'slot_count';

const hour_start_var = 'hour_start';
const minute_start_var = 'minute_start';
const hour_end_var = 'hour_end';
const minute_end_var = 'minute_end';

const hour_start_female_var = 'hour_start_female';
const minute_start_female_var = 'minute_start_female';
const hour_end_female_var = 'hour_end_female';
const minute_end_female_var = 'minute_end_female';

const validation_error_class = 'validation_error';


const slot_start_date_label = 'Start Timeslots On';

function hourmin_start(){
    return hourmin(hour_start_var,minute_start_var,"8");
}

function hourmin_end(){
    return hourmin(hour_end_var,minute_end_var,"13");
}

function hourmin_start_female(){
    return hourmin(hour_start_female_var,minute_start_female_var,"10");
}

function hourmin_end_female(){
    return hourmin(hour_end_female_var,minute_end_female_var,"11");
}

function hourmin($hid = "hour", $mid = "minute", $hval = "00", $mval = "00")
{
    $hours = array("00", 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23);


    $out = "<select name='$hid' id='$hid'>";
    foreach($hours as $hour)
        if(intval($hval) == intval($hour)) {
            $out .= "<option value='$hour' selected>$hour</option>";
        }
        else{
            $out .= "<option value='$hour'>$hour</option>";
        }

    $out .= "</select>";
    $out .= " : ";

    $minutes = array("00", 15, 30, 45);
    $out .= "<select name='$mid' id='$mid'>";

    foreach($minutes as $minute)
        if(intval($mval) == intval($minute)) {
        $out .= "<option value='$minute' selected>$minute</option>";
        }
        else {
            $out .= "<option value='$minute'>$minute</option>";
        }
    $out .= "</select>";



    return $out;
}

function booking_agent_visitation_calendar_function(){

    // We will display the full slots based on the date

    visitations_one_day_list_page();
    $visit_date = date('Y-m-d');

    if(!empty($_GET['visit_date'])) {
        $visit_date = $_GET['visit_date'];
    }

    $slot = '';
    if(!empty($_GET['slot_number'])) {
        $is_subpage = true;

    }

    ?>

    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />

    <p class="submit">



        <?php if ($is_subpage){
            ?>
            <a href="./admin.php?page=visitation-calendar" type="submit" name="visitationCalendarLink" id="visitationCalendarLink" class="button button-secondary" value="Go to Visitation Calendar">Go to Visitation Calendar</a>
            <?php
        }
        ?>

        <a href="./users.php?page=all_inmates_list" type="submit" name="inmageListLink" id="inmageListLink" class="button button-secondary" value="Go to Inmate List">Go to Inmate List</a>

        <a href="./users.php?page=all_visitors_list" type="submit" name="visitorListLink" id="visitorListLink" class="button button-secondary" value="Go to Visitor List">Go to Visitor List</a>
        <?php
        if(!empty($_GET['source'])) {
            if (!empty($_GET['inmate_id'])) {
                echo '<a type="submit" href="./users.php?page=all_inmates_list&action=view&inmate_id=' . $_GET['inmate_id'] . '" class="button button-secondary">Return to Inmate</a>';
            } else {
                if (!empty($_GET['visitor_id'])) {
                    echo '<a type="submit" href="./users.php?page=all_visitors_list&action=view&visitor_id=' . $_GET['visitor_id'] . '" class="button button-secondary">Return to Visitor</a>';
                }
            }
        }
        ?>
    </p>
    </form>

    <?php

}




function booking_agent_time_slot_generation_function()
{

    global  $wpdb;
    $prefix = $wpdb->base_prefix;

    // check user capabilities
    if (!current_user_can('manage_visitation_calendar_capability')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?= esc_html(get_admin_page_title()); ?></h1>

        <?php
            if(!empty($_GET[GENERATE_SLOTS])){
                $slot_result = generateSlots();
            }

        ?>

    <?php generate_slot_generation_form()?>


    </div>
    <?php
}

function generateSlots(){

    global $wpdb;
    $prefix = $wpdb->base_prefix;

    echo 'Generating slots, validating parameters...<br/>';

    $error_list = '';




    $error_list .= check_if_field_is_empty(SLOT_START_DATE_VAR, slot_start_date_label);


    if (empty($error_list)){
        echo ('All inputs valid, generating calendar...<br/>');



        $slot_start_date = $_GET[SLOT_START_DATE_VAR];
        $slot_end_date = $_GET[SLOT_END_DATE_VAR];

        $hour_start = $_GET[hour_start_var];
        $minute_start = $_GET[minute_start_var];

        $hour_end = $_GET[hour_end_var];
        $minute_end = $_GET[minute_end_var];

        $hour_female_start = $_GET[hour_start_female_var];
        $minute_female_start = $_GET[minute_start_female_var];

        $hour_female_end = $_GET[hour_end_female_var];
        $minute_female_end = $_GET[minute_female_end_var];

        $slot_length = $_GET[slot_length_var];
        $slot_count = $_GET[slot_count_var];

        $start_date_as_time = strtotime($slot_start_date);
        $end_date_as_time = strtotime($slot_end_date);

        $current_date_generation = $start_date_as_time;

        while($current_date_generation <= $end_date_as_time){
            $nice_current_date = date("Y-m-d", $current_date_generation);


            $current_hour = $hour_start;
            $current_minute = $minute_start;

            while (should_generate_slot($current_hour, $current_minute, $hour_end, $minute_end, $slot_length )){

                $current_hour_end = calculate_end_hour($current_hour, $current_minute,$slot_length );

                $current_minute_end = calculate_end_minute($current_hour, $current_minute,$slot_length );


                echo 'Timeslot is ' . $nice_current_date . ' - ' . $current_hour . ' : '. $current_minute .
                    ' to ' . $current_hour_end . ' : ' .$current_minute_end . '<br/>';

                $gender = determine_slot_gender($current_hour, $current_minute, $hour_female_start, $minute_female_start,  $hour_female_end, $minute_female_end  );

                /*
                 *   `VISIT_DATE` date NOT NULL,
                  `VISIT_HOUR_START` int(11) NOT NULL,
                  `VISIT_MINUTE_START` int(11) NOT NULL,
                  `VISIT_HOUR_END` int(11) NOT NULL,
                  `VISIT_MINUTE_END` int(11) NOT NULL,
                  `SLOT_NUMBER` int(11) NOT NULL,
                  `GENDER_ALLOWED` varchar(50) NOT NULL,
                 */
                for ($slot_index = 1; $slot_index <= $slot_count; $slot_index++ ){
                    $wpdb->insert($prefix . 'inmate_visitations',
                         array('VISIT_DATE' => $nice_current_date,
                        'VISIT_HOUR_START' => $current_hour,
                        'VISIT_MINUTE_START' => $current_minute,
                        'VISIT_HOUR_END' => $current_hour_end,
                        'VISIT_MINUTE_END' => $current_minute_end,
                         'SLOT_NUMBER' => $slot_index,
                        'GENDER_ALLOWED' => $gender,
                             'VISITATION_STATUS_CODE' => 'Available'
                         ));
                }

                $current_hour = $current_hour_end;
                $current_minute = $current_minute_end;
            }


            echo ('Slots generated for ' . $nice_current_date . '<br/>');


            $current_date_generation = $current_date_generation + (3600 * 24); //add seconds of one day
            // $next_date = date("Y-m-d", $next_date_as_time);
            // $next_month = date("Y-m-d", $next_month_as_time);

        }

    } else {
        echo '<ul>'.$error_list.'</ul>';
    }

}

function should_generate_slot($current_hour, $current_minute, $hour_end, $minute_end, $slot_length ){
    $total_minutes = $current_hour*60 + $current_minute + $slot_length;
    $end_minutes = $hour_end * 60 + $minute_end;

    return $total_minutes <= $end_minutes;
}

function calculate_end_hour($current_hour, $current_minute,$slot_length ){

    $total_minutes = $current_hour*60 + $current_minute + $slot_length;

    return floor($total_minutes/60);

}

function  calculate_end_minute($current_hour, $current_minute,$slot_length ){
    $total_minutes = $current_hour*60 + $current_minute + $slot_length;
    return $total_minutes % 60;
}

function determine_slot_gender($current_hour, $current_minute,
                               $hour_female_start, $minute_female_start,
                               $hour_female_end, $minute_female_end  ){


        if (($current_hour*60 + $current_minute) >= ($hour_female_start*60+$minute_female_start)
           &&
            ($current_hour*60 + $current_minute) < ($hour_female_end*60+$minute_female_end)
          ){
            return 'Female';
        } else {
            return 'Male';
        }

}

/**
 * @param $slot_var
 * @param $slot_label
 * @return string
 */
function check_if_field_is_empty($slot_var, $slot_label)
{
    if (!empty($_GET['debug'])) {
        echo 'Current var being validated is ' . $slot_var . ' it has value ' . $_GET[$slot_var] . '<br/>';
    }
    $currentError = '';
    if (empty($_GET[$slot_var])) {


        $currentError = '<li class=" . validation_error_class . "> Please enter a value for ' . $slot_label . '.</li>';
        return $currentError;

    }
    return $currentError;
}

function generate_slot_generation_form()
{
    global  $wpdb;
    $prefix = $wpdb->base_prefix;

    ?>
    <form id="add_inmate" method="get">

        <?php
        $max_visit_query = 'select max(visit_date) as MAX_VISIT from ' . $prefix . 'inmate_visitations;';

        $max_visit = $wpdb->get_row($max_visit_query)->MAX_VISIT;

        $max_visit_time = strtotime($max_visit);
        $next_date_as_time = $max_visit_time + (3600 * 24); //add seconds of one day

        $next_month_as_time = $next_date_as_time + (3600 * 24 * 30); //add seconds of one day
        $next_date = date("Y-m-d", $next_date_as_time);
        $next_month = date("Y-m-d", $next_month_as_time);

        ?>

        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>

        <h3>Generate New Visit Slots</h3>
        <p>Currently, the last visit slot is available on <?php echo $max_visit ?></p>
        <table class="form-table">
            <tr>
                <th><label for="<?= SLOT_START_DATE_VAR ?>"><?= slot_start_date_label ?> (Inclusive)</label></th>
                <td><input type="date" name="<?= SLOT_START_DATE_VAR ?>"
                           value="<?php echo $next_date; ?>"
                           class="regular-text"/></td>
            </tr>
            <tr>
                <th><label for="<?= SLOT_END_DATE_VAR ?>">End Timeslots On (Inclusive)</label></th>
                <td><input type="date" name="<?= SLOT_END_DATE_VAR ?>"
                           value="<?= $next_month; ?>"
                           class="regular-text"/></td>
            </tr>

            <tr>
                <th><label for="slot_start_time">Start Timeslots Time</label></th>
                <td><?php echo hourmin_start() ?> </td>
            </tr>

            <tr>
                <th><label for="slot_end_time">End Timeslots Time</label></th>
                <td><?php echo hourmin_end() ?> </td>
            </tr>


            <tr>
                <th><label for="slot_start_time_female">Female Start Timeslots </label></th>
                <td><?php echo hourmin_start_female() ?> </td>
            </tr>

            <tr>
                <th><label for="female_slot_end_time_female">End Timeslots Time</label></th>
                <td><?php echo hourmin_end_female() ?> </td>
            </tr>
            <tr>
                <th><label for="<?= slot_length_var ?>">Slot Length (minutes)</label></th>
                <td><input type="text" name="<?= slot_length_var ?>"
                           value="30"
                           class="regular-text"/></td>
            </tr>


            <tr>
                <th><label for="<?= slot_count_var ?> ">Slot Count (concurrent visits)</label></th>
                <td><input type="text" name="<?= slot_count_var ?>"
                           value="5"
                           class="regular-text"/></td>
            </tr>




        </table>
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>

        <p class="submit"><input type="submit" name="<?= GENERATE_SLOTS ?>" id="<?= GENERATE_SLOTS ?>" class="button button-primary"
                                 value="Generate Time Slots">

        </p>
    </form>

    <?php
}

/*
Date visitDate;
while (visitDate < lastVisitDate){

    for (int i = 1 ; i <= slot_count; i++){

        visit_start_hour = hour
		visit_start_minute = minute
		visit_end_hour calculate end based on slot length
		visit_end minute calculate end based on slot length
		slotsLeft = true;
		while(slotsLeft){
            if (is_female_visit_slot){
                insert record wit start, end, slot, female
		} else {
                insert record wit start, end, slot, female
		}
            visit_start_hour = visit_end_hour
		visit_start_minute = visit_end_minute
		visit_end_hour calculate end based on slot length
		visit_end minute calculate end based on slot length

		if (!canStillScheduleASlot){
            slotsLeft = false;
        }
		}

}

incrementVisitDate
}*/

?>