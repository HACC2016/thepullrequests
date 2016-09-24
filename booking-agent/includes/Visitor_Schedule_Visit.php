<?php
/**
 * Created by PhpStorm.
 * User: sidsavara
 * Date: 9/23/16
 * Time: 10:04 PM
 */

function booking_agent_visitor_schedule_visit_function(){

    $current_user = wp_get_current_user();
    $wp_user_id = $current_user->ID;
    $visitor_data = get_visitor_data_by_wp_user_id($wp_user_id);

    //schedule-visit&action=schedule&inmate_id=A014C44232
    if (isset($_GET['action'])){
        $action = $_GET['action'];
        if ($action == 'schedule') {
            $inmate_id = $_GET['inmate_id'];
            generate_time_table_to_visit($inmate_id, $wp_user_id);
        } else if ($action == 'schedule-slot'){
            // action=schedule-slot&inmate_id=A014C44232&visitation_id=6838&user_id=3
            $inmate_id = $_GET['inmate_id'];
            $visitation_id  = $_GET['visitation_id'];
            $wp_user_id = $_GET['user_id'];
            schedule_visit($inmate_id, $wp_user_id, $visitation_id);
            $visitation_info = get_visitor_slot_details($visitation_id);


            /*
             * visit_date, '.
                        ' visit_date, '.
                        ' visit_minute_start, '.
                        ' visit_hour_start, '.
                    ' visit_minute_end, '.
                    ' visit_hour_end, '.
                    ' i.inmate_name ' .
             */
            ?>

                <h2>Your visit has been scheduled and confirmed for:   </h2>
                <ul>
                    <li>Date: <?=$visitation_info->visit_date ?></li>
                    <li>Time: <?=str_pad($visitation_info->visit_hour_start,2,0,STR_PAD_LEFT); ?>:<?=str_pad($visitation_info->visit_minute_start,2,0,STR_PAD_LEFT); ?></li>
                    <li>Inmate: <?=$visitation_info->inmate_name ?></li>

                </ul>

            You will receive an email confirmation.

            <?php

            $headers = "From: occcadmin@hawaii.gov\r\n";

            $msg = 'Date: '.$visitation_info->visit_date . "\r\n";
            $msg .= 'Time: '.str_pad($visitation_info->visit_hour_start,2,0,STR_PAD_LEFT). ' : ' .str_pad($visitation_info->visit_minute_start,2,0,STR_PAD_LEFT) . "\r\n";
            $msg .= 'Inmate: '.$visitation_info->inmate_name . "\r\n";

            $msg .= 'Please review the visitation procedures prior to your arrival.';

            mail($current_user->user_email,"Your visit has been scheduled With : " . $visitation_info->inmate_name,
                $msg, $headers);


        } else if ($action == 'cancel-slot'){
            $visitation_id  = $_GET['visitation_id'];
            cancel_visit($wp_user_id, $visitation_id);
        }
    } else {

    }

    generate_inmates_table_to_visit($wp_user_id);


}

function get_visitor_slot_details ($visitation_id){
    global $wpdb;
    $prefix = $wpdb->base_prefix;




    $visit_rel_query = ' select visit_date, '.
                        ' visit_date, '.
                        ' visit_minute_start, '.
                        ' visit_hour_start, '.
                    ' visit_minute_end, '.
                    ' visit_hour_end, '.
                    ' i.inmate_name ' .
        ' from '.$prefix.'inmate_visitations vis, ' .
        ' '.$prefix.'inmates i ' .
        " where vis.visitation_id = " . $visitation_id
        . ' AND vis.inmate_id = i.inmate_id '
        . ';';

    // echo $visit_rel_query;
    return $wpdb->get_row($visit_rel_query);


}

function schedule_visit($inmate_id, $wp_user_id, $visitation_id) {
    global $wpdb;
    $prefix = $wpdb->base_prefix;
    $visitor_rel_id = get_visitor_rel_id($inmate_id, $wp_user_id);

    $wpdb->update($wpdb->prefix . 'inmate_visitations', array(
        'visitor_1_id' => $visitor_rel_id,
        'inmate_id' =>$inmate_id,
        'visitation_status_code' => 'Scheduled'),
        array('visitation_id'=>$visitation_id),
        array('%s','%s','%s'),
        array('%s') );
}

function get_visitor_rel_id($inmate_id, $wp_user_id){
    global $wpdb;
    $prefix = $wpdb->base_prefix;




    $visit_rel_query = ' select visit_rel_id '.
        ' from '.$prefix.'inmate_visitors vis, '.
        $prefix . 'inmates i ' .
        " where i.inmate_id = vis.inmate_id " .
        ' and vis.wp_user_id = ' . $wp_user_id .
        " and i.inmate_id = '" . $inmate_id . "';";

    // echo $visit_rel_query;

        return $wpdb->get_row($visit_rel_query)->visit_rel_id;


}

function generate_time_table_to_visit($inmate_id, $wp_user_id) {
    global $wpdb;
    $prefix = $wpdb->base_prefix;




    $visitation_table_query = ' select visit_date, '.
                             'visit_hour_start, '.
                            'visit_minute_start, '.
                            ' min(visitation_id) as visitation_id' .
                            ' from '.$prefix.'inmate_visitations vis, '.
                            $prefix . 'inmates i ' .
                            " where vis.visitation_status_code = 'Available' " .
                            ' and i.gender = vis.gender_allowed ' .
                            " and i.inmate_id = '" . $inmate_id . "'".
                            ' and visit_date > CURDATE() ' .
                            ' AND visit_date < CURDATE() + INTERVAL 7 DAY ' .
                            ' group by visit_date, visit_hour_start, visit_minute_start; ' ;


    // echo $visitors_query;
    $visitation_results = $wpdb->get_results($visitation_table_query);

    // echo $visitors_query;
    /*
     * foreach( $wpdb->get_results("SELECT * FROM your_table_name WHERE id LIKE' . $id . ';") as $key => $row) {
// each column in your row will be accessible like this
$my_column = $row->column_name;}
     */

    ?>
    <h3><span class="dashicons dashicons-groups"></span> Select Visitation Time Inmate</h3>

    <br/><br/>

    <?php

    if (count($visitation_results) > 0) {

        ?>


        <table class="widefat striped wp-list-table widefat fixed ">
            <thead>
            <tr>
                <th>Date and Time</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th>Date and Time</th>
            </tr>
            </tfoot>


            <tbody>
            <?php
            foreach ($visitation_results as $key => $visitation_slot) {

                ?>

                <tr>
                    <td><a href="?page=schedule-visit&action=schedule-slot&inmate_id=<?= $inmate_id?>&visitation_id=<?=$visitation_slot->visitation_id?>&user_id=<?= $wp_user_id ?>">
                            <?=$visitation_slot->visit_date; ?> at
                            <?=str_pad($visitation_slot->visit_hour_start,2,0,STR_PAD_LEFT); ?>:<?=str_pad($visitation_slot->visit_minute_start,2,0,STR_PAD_LEFT); ?></a></td>

                </tr>

                <?php

            }

            ?>

            </tbody>
        </table>
        <br/>



        <?php
    } else {
        ?>
        <h4><em>You are currently not approved to visit any inmates</em></h4>
        <?php
    }
}

function generate_inmates_table_to_visit($wp_user_id  )
{
    global $wpdb;
    $prefix = $wpdb->base_prefix;


    $where_clause = ' AND v.wp_user_id = ' . $wp_user_id;


    $visitors_query = "SELECT v.RELATIONSHIP, " .
        " v.APPROVAL_DATE, " .
        " v.VISITOR_STATUS, " .
        " v.VISITOR_MESSAGE, " .
        "i.INMATE_ID," .
        " i.INMATE_NAME," .
        " i.POD_CELL, " .
        " i.INC_DATE, " .
        "CONCAT(i.VISIT_CODE, '  - ', c.description ) as VISIT_CODE " .
        "  FROM " .
        $prefix . "inmate_visitors v , " .
        $prefix . "inmates i," .
        $prefix . "inmate_visitation_status c" .
        " WHERE i.VISIT_CODE = c.VISITATION_STATUS_CODE " .
        " AND i.INMATE_ID = v.INMATE_ID " .
        $where_clause .
        " ORDER BY i.INMATE_NAME ;";


    // echo $visitors_query;
    $inmates = $wpdb->get_results($visitors_query);

    // echo $visitors_query;
    /*
     * foreach( $wpdb->get_results("SELECT * FROM your_table_name WHERE id LIKE' . $id . ';") as $key => $row) {
// each column in your row will be accessible like this
$my_column = $row->column_name;}
     */

    ?>
    <h3><span class="dashicons dashicons-groups"></span> Request Visit With Inmate</h3>

    <br/><br/>

    <?php

    if (count($inmates) > 0) {

        ?>


        <table class="widefat striped wp-list-table widefat fixed ">
            <thead>
            <tr>
                <th>Name</th>
                <th>Relationship</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th>Name</th>
                <th>Relationship</th>
            </tr>
            </tfoot>


            <tbody>
            <?php
            foreach ($inmates as $key => $inmate) {

                ?>

                <tr>
                    <td><a href="?page=schedule-visit&action=schedule&inmate_id=<?php echo $inmate->INMATE_ID?>">Schedule With <?php echo $inmate->INMATE_NAME; ?></a></td>
                    <td><?php echo $inmate->RELATIONSHIP; ?></td>

                </tr>

                <?php

            }

            ?>

            </tbody>
        </table>
        <br/>



        <?php
    } else {
        ?>
        <h4><em>You are currently not approved to visit any inmates</em></h4>
        <?php
    }

}