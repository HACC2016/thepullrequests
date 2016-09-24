<?php
/**
 * Created by PhpStorm.
 * User: sidsavara
 * Date: 9/23/16
 * Time: 7:06 PM
 */



//1. Add a new form element...
add_action( 'register_form', 'booking_agent_visitor_register_form' );
function booking_agent_visitor_register_form() {

    $inmate_id = ( ! empty( $_POST['inmate_id'] ) ) ? trim( $_POST['inmate_id'] ) : '';

    $pin = ( ! empty( $_POST['pin'] ) ) ? trim( $_POST['pin'] ) : '';

    $date_of_birth = ( ! empty( $_POST['date_of_birth'] ) ) ? trim( $_POST['date_of_birth'] ) : '';

    $relationship = ( ! empty( $_POST['relationship'] ) ) ? trim( $_POST['relationship'] ) : '';

    $visitor_name = ( ! empty( $_POST['visitor_name'] ) ) ? trim( $_POST['visitor_name'] ) : '';

    $phone_number = ( ! empty( $_POST['phone_number'] ) ) ? trim( $_POST['phone_number'] ) : '';



    ?>
    <p>
        <label for="inmate_id"><?php _e( 'Inmate ID', 'booking-agent' ) ?><br />
            <input type="text" name="inmate_id" id="inmate_id" class="input" value="<?php echo esc_attr( wp_unslash( $inmate_id ) ); ?>" size="25" /></label>
    </p>

    <p>
        <label for="pin"><?php _e( 'Create Your Pin', 'booking-agent' ) ?><br />
            <input type="text" name="pin" id="pin" class="input" value="<?php echo esc_attr( wp_unslash( $pin ) ); ?>" size="25" /></label>
    </p>

    <p>
        <label for="phone_number"><?php _e( 'Your Phone (Cell for Text)', 'booking-agent' ) ?><br />
            <input type="text" name="phone_number" id="phone_number" class="input" value="<?php echo esc_attr( wp_unslash( $phone_number ) ); ?>" size="25" /></label>
    </p>

    <p>
        <label for="visitor_name"><?php _e( 'Your Name', 'booking-agent' ) ?><br />
            <input type="text" name="visitor_name" id="visitor_name" class="input" value="<?php echo esc_attr( wp_unslash( $visitor_name ) ); ?>" size="25" /></label>
    </p>

    <p>
        <label for="date_of_birth"><?php _e( 'Your Date of Birth', 'booking-agent' ) ?><br />
            <input type="date" name="date_of_birth" id="date_of_birth" class="input" value="<?php echo esc_attr( wp_unslash( $date_of_birth ) ); ?>" size="25" /></label>
    </p>

    <p>
        <label for="relationship"><?php _e( 'Relationship to Inmate', 'booking-agent' ) ?><br />
            <input type="text" name="relationship" id="relationship" class="input" value="<?php echo esc_attr( wp_unslash( $relationship ) ); ?>" size="25" /></label>
    </p>

    <?php
}


//3. Finally, save our extra registration user meta.
add_action( 'user_register', 'booking_agent_visitor_register_submit' );

add_action( 'profile_update', 'booking_agent_visitor_profile_update', 10, 2 );

function booking_agent_visitor_profile_update( $user_id, $old_user_data ) {



    global $wpdb;

    $prefix = $wpdb->base_prefix;


    $pin = ( ! empty( $_POST['pin'] ) ) ? trim( $_POST['pin'] ) : '';

    $date_of_birth = ( ! empty( $_POST['date_of_birth'] ) ) ? trim( $_POST['date_of_birth'] ) : '';

// cannot update these two yet
    $inmate_id = ( ! empty( $_POST['inmate_id'] ) ) ? trim( $_POST['inmate_id'] ) : '';

    $relationship = ( ! empty( $_POST['relationship'] ) ) ? trim( $_POST['relationship'] ) : '';

    $visitor_name = ( ! empty( $_POST['visitor_name'] ) ) ? trim( $_POST['visitor_name'] ) : '';

    $visitor_status = ( ! empty( $_POST['status'] ) ) ? trim( $_POST['status'] ) : '';

    $visitor_message = ( ! empty( $_POST['visitor_message'] ) ) ? trim( $_POST['visitor_message'] ) : '';

    $phone_number = ( ! empty( $_POST['phone_number'] ) ) ? trim( $_POST['phone_number'] ) : '';

    $old_visitor_data = get_visitor_data_by_wp_user_id($user_id);

    if ($old_visitor_data->VISITOR_STATUS != $visitor_status) {
        inform_visitor_status_changer($old_visitor_data->VISITOR_STATUS, $visitor_status, $phone_number, $user_id);
    }

    $wpdb->update($prefix . 'inmate_visitors', array(
        'visitor_name' => $visitor_name,
        'visitor_pin' => $pin,
        'visitor_message' => $visitor_message,
        'phone_number' => $phone_number,
        'date_of_birth' => $date_of_birth,
        'visitor_status' => $visitor_status),
        array('wp_user_id'=>$user_id),
        array('%s','%s','%s','%s', '%s', '%s'),
        array('%s') );


}

function inform_visitor_status_changer($old_visitor_status, $visitor_status, $phone_number, $user_id) {
    $wp_user = get_user_by('id', $user_id);

    $headers = "From: occcadmin@hawaii.gov\r\n";

    $msg = 'Your account status has changed from ' . $old_visitor_status . ' to ' . $visitor_status;

    if (!empty($phone_number)) {
     //   mail($wp_user->user_email,"Account Status Changed to: " . $visitor_status, 'Text to '.$phone_number.': '. $msg, $headers);

        // removed text for now
        /* $courier = new Courier;

        $courier->setRecipient($phone_number)->setBody($msg)->send(); */
    }

    $msg = 'Your account status has changed from ' . $old_visitor_status . ' to ' . $visitor_status;

    mail($wp_user->user_email,"Account Status Changed to: ". $visitor_status,$msg, $headers);
}

function booking_agent_visitor_register_submit( $user_id ) {

    global $wpdb;

    $prefix = $wpdb->base_prefix;

    $inmate_id = ( ! empty( $_POST['inmate_id'] ) ) ? trim( $_POST['inmate_id'] ) : '';

    $pin = ( ! empty( $_POST['pin'] ) ) ? trim( $_POST['pin'] ) : '';

    $date_of_birth = ( ! empty( $_POST['date_of_birth'] ) ) ? trim( $_POST['date_of_birth'] ) : '';

    $relationship = ( ! empty( $_POST['relationship'] ) ) ? trim( $_POST['relationship'] ) : '';

    $visitor_name = ( ! empty( $_POST['visitor_name'] ) ) ? trim( $_POST['visitor_name'] ) : '';

    $phone_number = ( ! empty( $_POST['phone_number'] ) ) ? trim( $_POST['phone_number'] ) : '';


    $wpdb->insert($prefix . 'inmate_visitors', array('inmate_id' => $inmate_id,
        'visitor_name' => $visitor_name,
        'phone_number' => $phone_number,
        'visitor_pin' => $pin,
        'visitor_message' => 'Thank you for registering, staff are reviewing your information',
        'relationship' => $relationship,
        'date_of_birth' => $date_of_birth,
        'visitor_status' => 'Pending'),
        array('%s','%s', '%s', '%s','%s', '%s', '%s','%s' ) );


}


add_action( 'show_user_profile', 'add_visitor_fields_to_profile' );
add_action( 'edit_user_profile', 'add_visitor_fields_to_profile' );
add_action( "user_new_form", "add_visitor_fields_to_profile" );

function add_visitor_fields_to_profile($user){

    // echo $visitor_id;
    global $wpdb;
    $prefix = $wpdb->base_prefix;

    $current_user = wp_get_current_user();
    $is_current_admin = false;

    if ( is_visitor_role_only( $user ) ) {



        if (!is_visitor_role_only( $current_user )) {
            $is_current_admin = true;
        }
    $wp_user_id = $user->ID;

        $visitor_data = get_visitor_data_by_wp_user_id($wp_user_id);


        $inmate_query = "SELECT " .
            " vis.VISITOR_NAME, " .
            " vis.VISITOR_PIN, " .
            " i.INMATE_ID,".
            " vis.VISIT_REL_ID,".
            " i.INMATE_NAME,".
            " vis.RELATIONSHIP, " .
            " vis.DATE_OF_BIRTH, " .
            " vis.VISITOR_PIN, " .
            " vis.APPROVAL_DATE, " .
            " vis.VISITOR_STATUS,
		 vis.VISITOR_MESSAGE," .
            "CONCAT(i.VISIT_CODE, '  - ', v.description ) as VISIT_CODE " .
            "  FROM " .
            $prefix . "inmate_visitors vis left join " .
            $prefix . "inmates i " .
            " on vis.INMATE_ID = i.INMATE_ID left join " .
            $prefix . "inmate_visitation_status v " .
            " on i.VISIT_CODE = v.VISITATION_STATUS_CODE " .
            " WHERE vis.VISIT_REL_ID = " . $visitor_data->VISIT_REL_ID . ";" ;

        ?>
        <h3>Visitor Profile</h3>

        <table class="form-table">

            <tr>
                <th><label for="inmate_name">Visitor Name</label></th>
                <td><input type="text" name="visitor_name"
                           value="<?php echo $visitor_data->VISITOR_NAME ?>"
                           class="regular-text"/></td>
            </tr>

            <tr>
                <th><label for="pin">PIN</label></th>
                <td><input type="text" name="pin"
                           value="<?php echo $visitor_data->VISITOR_PIN ?>"
                           class="regular-text"/></td>
            </tr>

            <tr>
                <th><label for="phone_number">Phone Number</label></th>
                <td><input type="text" name="phone_number"
                           value="<?php echo $visitor_data->PHONE_NUMBER ?>"
                           class="regular-text"/></td>
            </tr>

            <tr>
                <th><label for="date_of_birth">Date of Birth</label></th>
                <td><input type="date" name="date_of_birth"
                           value="<?php echo $visitor_data->DATE_OF_BIRTH ?>"
                           class="regular-text"/></td>
            </tr>



            <!--<select>
      <option value="volvo">Volvo</option>

      <option value="audi" selected>Audi</option>
    </select> -->



            <tr>
                <th><label for="status">Visit Approval Status</label></th>

                <?php
                if ($is_current_admin) {

                    ?>

                    <td><select name="status">
                            <?php


                            $visit_code_query = "SELECT STATUS," .
                                " DESCRIPTION " .
                                "  FROM " .
                                $prefix . "inmate_visitor_statuses v";

                            echo $visit_code_query;
                            $status_data = $wpdb->get_results($visit_code_query);

                            foreach ($status_data as $key => $code) {
                                $selected = '';
                                if ($code->STATUS == $visitor_data->VISITOR_STATUS) {
                                    $selected = "selected";
                                }
                                echo '<option ' . $selected . ' value="' . $code->STATUS . '">' . $code->STATUS . ' - ' . $code->DESCRIPTION . '</option>';
                            }

                            ?>
                        </select></td>
                    <?php
                } else {

                    $visit_code_query = "SELECT STATUS," .
                        " DESCRIPTION " .
                        "  FROM " .
                        $prefix . "inmate_visitor_statuses v " .
                        " WHERE v.STATUS = '" . $visitor_data->VISITOR_STATUS . "';";

                   // echo $visit_code_query;

                    $status_data = $wpdb->get_row($visit_code_query);

                    echo '<td> ' . $status_data->STATUS . ' - ' . $status_data->DESCRIPTION . '</td>' ;
                }
                    ?>
            </tr>

            <?php
                $readonly = 'readonly';

                if ($is_current_admin) {
                    $readonly ='';
                }
            ?>

            <tr>
                <th><label for="visitor_message">Visitor Message</label></th>
                <td><textarea name="visitor_message"
                              style="width: 600px; height: 100px;"
                              <?= $readonly ?>
                              class="regular-text"><?php echo $data->VISITOR_MESSAGE ?></textarea></td>
            </tr>
        </table>
        <?php
    }

}

/**
 * @param $prefix
 * @param $wp_user_id
 * @param $wpdb
 * @return mixed
 */
function get_visitor_data_by_wp_user_id($wp_user_id)
{

    // echo $visitor_id;
    global $wpdb;
    $prefix = $wpdb->base_prefix;
    $visitor_query = "SELECT " .
        " vis.VISITOR_NAME, " .
        " vis.VISITOR_PIN, " .
        " vis.VISIT_REL_ID," .
        " vis.RELATIONSHIP, " .
        " vis.DATE_OF_BIRTH, " .
        " vis.VISITOR_PIN, " .
        " vis.APPROVAL_DATE, " .
        " vis.PHONE_NUMBER, " .
        " vis.VISITOR_STATUS,
		 vis.VISITOR_MESSAGE " .
        "  FROM " .
        $prefix . "inmate_visitors vis " .
        " WHERE vis.wp_user_id = " . $wp_user_id . ";";


    // echo $visitor_query;
    $visitor_data = $wpdb->get_row($visitor_query);
    return $visitor_data;
}