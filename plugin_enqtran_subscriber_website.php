<?php
/**
* Plugin Name: [Enqtran] Subscriber website
* Plugin URI: http://enqtran.com/
* Description: Subscriber website auto send email to admin and subscriber.
* Author: enqtran
* Version: 1.0
* Author URI: http://enqtran.com/
* Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=EU3YV2GB9434U
* License: GPLv3 or later
* License URI: http://www.gnu.org/licenses/gpl-3.0.html
* Tags: enqtran, enq, enqpro, send mail, subscriber, follow ...
*/

/*
* Plugin status
* Last update: 09/12/2015
*/
add_action( 'widgets_init', 'subscriber_enqtran_widget' );
if ( !function_exists('subscriber_enqtran_widget') ) {
    function subscriber_enqtran_widget() {
        register_widget('Enqtran_Subscriber_Widget');
    }
}
class Enqtran_Subscriber_Widget extends WP_Widget {

/**
 * config widget
 */
function __construct() {
    $widget_ops = array(
            'sub_email_widget', // id
            'description'=>'[Enqtran] Subscriber website'
        );
     parent::__construct( '', '[Enqtran] Subscriber website', $widget_ops );
}

/**
 * [form admin]
 */
function form( $instance ){
    $defaults = array(
        'title' => ''
        );
    $instance = wp_parse_args( $instance, $defaults );
    $title = esc_attr($instance['title']);
?>

<!-- show form admin -->
<div class="box-w">
    <p>
        <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title' ); ?></label>
    </p>
    <p>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" placeholder="Title for Widget" />
    </p>
</div>
<div class="box-w">
    <p>Default don't show</p>
</div>
<?php
}

/*
* [update]
*/
function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance['title'] = esc_attr($new_instance['title']);
    return $instance;
}

/**
* [widget content]
*/
function widget( $args, $instance ) {
    extract($args);
    $title = apply_filters( 'widget_title', $instance['title'] );
    echo $before_widget;
    if ( !empty( $title ) ) {
        echo $before_title;
        echo $title;
        echo $after_title;
    } ?>
    <div class="content-sidebar-widget">
        <div class="subscriberb_widget">
            <form action="" method="post" class="enqtran-send-mail form-horizontal ">
            <div class="">
                 <input type="email" name="email_sub_by_enqtran" class="enqtran_sub_emails" value="" placeholder="Email ...." required>
                <input type="submit" name="submit_email_sub_by_enqtran" class="submit_email_enqtran" value="Subscribe">
                <div class="megs_enqtran_sub"></div>

            </div>
            </form>
            <style>
                .enqtran_sub_emails{
                    color: #000;
                }
                .submit_email_enqtran{
                    color: #fff;
                }
                form.enqtran-send-mail input {
                    display: inline;
                    float: left;
                }
                input.enqtran_sub_emails {
                    color: #000 !important;
                    background: #fff !important;
                    padding: 5px 5px;
                    border:0px;
                }
                input.submit_email_enqtran {
                    background: #f25;
                    border: 0px;
                    padding: 5px;
                }
            </style>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
            <script>
                $(document).ready(function() {
                    $('.enqtran-send-mail').submit(function(event){
                        $.ajax({
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            type: 'POST',
                            data: $(this).serialize()+'&action=email_sub_action_enqtran_plugin',
                            dataType:"json",
                            // dataType: 'text',
                            beforeSend: function(){
                                jQuery('.megs_enqtran_sub').html('Send .... ');
                            }, //end before
                            success: function(result){
                                if ( result == 'error') {
                                    jQuery('.megs_enqtran_sub').html('Email error !');
                                    jQuery('.megs_enqtran_sub').css('color', 'red');
                                    setTimeout(function(){
                                        jQuery('.megs_enqtran_sub').html('');
                                        jQuery('.megs_enqtran_sub').css('color', '#fff');
                                    }, 2000);
                                }
                                if ( result == 'Subscriber' ) {
                                    console.log(result);
                                    jQuery('.megs_enqtran_sub').html('Subscriber');
                                    jQuery('.megs_enqtran_sub').css('color', 'green');
                                    jQuery('.enqtran_sub_emails').val('');
                                    setTimeout(function(){
                                        jQuery('.megs_enqtran_sub').html('');
                                        jQuery('.megs_enqtran_sub').css('color', '#fff');
                                    }, 2000);
                                }
                            } //end success
                        });
                        event.preventDefault();
                    });
                });
            </script>
        </div>
    </div>
<?php
    echo $after_widget;
    }
}
// end class widget

/**
 * Ajax action
 */
add_action('wp_ajax_email_sub_action_enqtran_plugin', 'email_sub_action_enqtran_plugin');
add_action('wp_ajax_nopriv_email_sub_action_enqtran_plugin', 'email_sub_action_enqtran_plugin');
function generateRandomStringEnqtranPlugins($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
// Email Notification to Admin
function email_sub_action_enqtran_plugin()
{
    $email = esc_attr($_POST['email_sub_by_enqtran']);
    // Remove all illegal characters from email
    $email = filter_var( $email, FILTER_SANITIZE_EMAIL );
    // Validate e-mail
    if ( !filter_var( $email, FILTER_VALIDATE_EMAIL ) === false ) {
        $userdata =  array(
            'user_email' => $email,
            'user_nicename' => $email,
            'user_login' => $email,
            'user_pass' => generateRandomStringEnqtranPlugins(),
            'role'       => 'subscriber',
        );
        $idUser = wp_insert_user( $userdata );
    } else {
        echo json_encode('error');
        exit();
    }
    if ( isset($idUser) ) {
        if ( get_option( 'on_send_admin' ) == 'on') {
            $to = get_option('admin_email');
            $subject = '[Plugins Subscriber Website ] - '.date('d-m-Y').' - '.$email;
            $body = "\n\n [Subscriber website].";
            $body .= "\n\n Link: ". get_site_url()."/wp-admin/themes.php?page=subscriber";
            $body .= "\n\n News email subscriber: ".$email;
            wp_mail( $to, $subject, $body );
        }
        echo json_encode('Subscriber');
    } else {
        echo json_encode('Error');
    }
    exit();
}

/**
 * Enable send mail to subscriber, have new articles
 */
if ( get_option( 'on_send_subscriber' ) == 'on') {
    add_action( 'save_post', 'enqtran_sub_plugin_updated_send_email' );
    function enqtran_sub_plugin_updated_send_email( $post_id ) {
         // If this is just a revision, don't send the email.
         if ( wp_is_post_revision( $post_id ) ) :
             return;
         endif;

         $post_title = get_the_title( $post_id );
         $post_url = get_permalink( $post_id );
         $subscribers = get_users( array ( 'role' => 'subscriber' ) );
            // $emails      = array ();

         $subject = "[News Post] ".$post_title;
         $messenger = "[News Post] ".$post_title;
            if ( get_option( 'messenger_email_to' ) ) {
                 $messenger .= "\n\n".get_option( 'messenger_email_to' );
            }
         $messenger .= "\n\n Link post: " . $post_url;
         $messenger .= "\n\n Thank Subscribe";
         // $messenger .= "\n\n Subscriber Website Plugin By Enqtran";

         // Send email
         if ( get_post_status( $post_id ) == 'publish') {
             foreach ( $subscribers as $subscriber ) :
                $emails = $subscriber->user_email;
                wp_mail( $emails, $subject, $messenger );
            endforeach;
         }
    }
}

add_action( 'admin_menu', 'register_enqtran_plugins_subscriber_page' );
function register_enqtran_plugins_subscriber_page() {
     add_theme_page( 'Subscriber', 'Subscriber', 'manage_options', 'subscriber', 'enqtran_plugin_subscriber_setting_page' );
}

add_action( 'admin_init' , 'enqtran_plugin_subscriber_page' );
function enqtran_plugin_subscriber_page() {
    //Turn Off Website Maintenance
    register_setting( 'enq-subscriber-group' , 'on_send_admin' );
    register_setting( 'enq-subscriber-group' , 'on_send_subscriber' );
    register_setting( 'enq-subscriber-group' , 'messenger_email_to' );
}

function enqtran_plugin_subscriber_setting_page() { ?>
    <div class="wrap">
        <?php echo get_screen_icon(); ?>
        <div>
            <h1 align="center"> Email Subscriber</h1>
        </div>
        <form action="options.php" method="post" id="theme_setting">
            <?php settings_fields( 'enq-subscriber-group' ); ?>
            <style>
                .fix-width {
                    width:150px;
                }
                .pad {
                    padding:10px 20px;
                }
            </style>

            <!-- Maintenance -->
            <h2> Option email </h2>
            <table class="theme_page widefat" >
                <tr>
                    <th class="fix-width">Email Admin</th>
                    <td>
                        <input type="checkbox" name="on_send_admin" <?php checked( get_option( 'on_send_admin' ), 'on');?> /> Send email to admin when news subscriber.
                    </td>
                </tr>
                <tr>
                    <th class="fix-width">Email Subscriber</th>
                    <td>
                        <input type="checkbox" name="on_send_subscriber" <?php checked( get_option( 'on_send_subscriber' ), 'on');?> /> Send email to subscriber when new post publish
                    </td>
                </tr>
                <tr>
                    <th>Messager</th>
                    <td>
                        <textarea name="messenger_email_to"  class="widefat" rows="4"><?php echo get_option( 'messenger_email_to' ); ?></textarea>
                    </td>
                </tr>
            </table>
            <?php submit_button( 'Save Changes','primary' ); ?>
        </form>
        <br>

        <h2> Latest subscriber </h2>
        <div class="box-form-option">
            <table class="box-table widefat">
                <thead>
                    <tr>
                        <td align="center" >STT</td>
                        <td align="center" >Email</td>
                        <td align="center" >Username</td>
                        <td align="center" >Role</td>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $number     = 10;
                    $paged      = (get_query_var('paged')) ? get_query_var('paged') : 1;
                    $offset     = ($paged - 1) * $number;
                    $users      = get_users('role=subscriber');
                    $list_email_sub = get_users('role=subscriber&offset='.$offset.'&number='.$number.'&orderby=registered&order=desc');
                    $total_users = count($users);
                    $total_query = count($list_email_sub);
                    $total_pages = intval($total_users / $number) + 1;

                    if ( ! empty( $list_email_sub ) ) {
                        $stt = 1;
                        foreach ( $list_email_sub as $user ) {
                            // echo "<pre>";
                            // print_r($user);
                            // echo "</pre>";
                            ?>
                            <tr>
                                <td ><?php echo $stt++; ?></td>
                                <td ><?php echo $user->user_email; ?></td>
                                <td ><?php echo $user->user_login; ?></td>
                                <td ><?php echo $user->roles['0']; ?></td>
                            </tr>
                    <?php  }
                    } else {
                        echo "<tr><td colspan='3' align='center'> Not user subscriber !</td></tr>";
                    }
                ?>
                </tbody>
            </table>
        </div>

        <div>
            <?php
                // if ($total_users > $total_query) {
                //     echo '<div id="pagination" class="clearfix">';
                //     echo '<span class="pages">Pages:</span>';
                //       $current_page = max(1, get_query_var('paged'));
                //       echo paginate_links(array(
                //             'base' => get_pagenum_link(1) . '%_%',
                //             'format' => '/page/%#%/',
                //             'current' => $current_page,
                //             'total' => $total_pages,
                //             'prev_next'    => false,
                //             'type'         => 'list',
                //         ));
                //     echo '</div>';
                // }
            ?>
        </div>
    </div>
<?php }


