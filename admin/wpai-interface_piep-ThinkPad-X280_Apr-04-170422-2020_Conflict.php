<?php

add_action('admin_menu', 'wpai_setup_menu');

function wpai_setup_menu()
{
    add_menu_page('WP Auto Installer', 'WP Auto Installer', 'manage_options', 'wpai', 'wpai_init');
}
            function sample_admin_notice__success()
            {
                ?>
                  <div class="notice notice-success is-dismissible">
                  <h2><?php _e( "Hi, welcome to WordPress.", 'wpai' ); ?>  </h2> 
             <p><?php _e( "Don't forgot to delete the WP Installer", 'wpai')  ?> </p>
                 
                  <?php echo '<form action="options-general.php?page=wpai" method="post">';
                                wp_nonce_field('delete_mu-plugin');
                                echo '<input type="hidden" value="true" name="delete_mu-plugin" />';
                                submit_button('Delete Installer');
                                echo '</form>';
                                ?>
 <?php echo '<form action="options-general.php?page=wpai" method="post">';
wp_nonce_field('send_logs_action');
echo '<input type="hidden" value="true" name="send_logs" />';
submit_button('Send Logs');
echo '</form>';
?>
   

    </div>
                <?php
            }
function wpai_interface()
{
    // General check for user permissions.
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient pilchards to access this page.'));
    }
    // Check whether the button has been pressed AND also check the nonce
    if (isset($_POST['send_logs']) && check_admin_referer('send_logs_action')) {
        // the button has been pressed AND we've passed the security check
        send_logs();
    }
    // Check whether the button has been pressed AND also check the nonce
    if (isset($_POST['delete_mu-plugin']) && check_admin_referer('delete_mu-plugin')) {
        // the button has been pressed AND we've passed the security check
        delete_mu_plugin();
    }

   
    $configdata = json_decode(file_get_contents('http://json.testing.threeelements.de/19'), true);
    $setup =  $configdata['setup'];
    create_local_setup_json($setup);

 
?>
    <div class="wrap">
       
            <div id="dashboard-widgets" class="metabox-holder">
                <div class="welcome-panel-content">
                <h2><?php _e( "Willkommen bei WordPress, powered by WP Auto Installer!", 'wpai' ); ?></h2>
                    <div>
                        <div id="dashboard-widgets" class="metabox-holder">
                            <div class="postbox-container">
                                <table class="widefat">
                                    <thead>
                                        <tr>
                                           <td><h3><?php _e( "Logs & Activity", 'wpai' ); ?></h3></td> 
                                        </tr>
                                    </thead>
 <tbody>
                                        <tr>
                                            <td><h3><?php _e( "Themes", 'wpai' ); ?></h3></td>
                                        </tr>
                                        <?php foreach ($setup['themes'] as $theme) : ?>
                                            <tr>
                                                <td>
                                                    <?php echo $theme['name']; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($theme['status'] == 'active') :
                                                        $theme = wp_get_theme(); // gets the current theme
                                                        if ($theme['name'] == $theme->name || $theme['name'] == $theme->parent_theme) :
                                                            echo '<span class="dashicons dashicons-yes"></span>';
                                                        else : echo '<span class="dashicons dashicons-no"></span>';
                                                        endif;
                                                    endif; ?>
                                                </td>

                                            </tr>
                                        <?php endforeach; ?>
                                        <tr>
                                            <td><h2><?php _e( "Plugins", 'wpai' ); ?></h2></td>
                                        </tr>
                                        <?php foreach ($setup['plugins'] as $plugin) : ?>
                                            <tr>
                                                <td>
                                                    <?php echo $plugin['name']; ?>
                                                </td>
                                                <td>
                                                    <?php if (is_plugin_active($plugin['path'] . '/' . $plugin['file'])) : echo '<span class="dashicons dashicons-yes"></span>';
                                                    else : echo '<span class="dashicons dashicons-no"></span>';
                                                    endif; ?>
                                                </td>

                                            </tr>
    
                                        <?php endforeach; ?>

                                        <tr>
                                            <td><h2><?php _e( "Options", 'wpai' ); ?></h2></td>
                                        </tr>
                                        <?php foreach ($setup['options'] as $option) : ?>
                                            <?php $local_option = get_option($option['key']); ?>

                                            <tr>
                                                <td>
                                                    <?php echo $option['key']; ?>


                                                </td>
                                                <td>
                                                    <?php if ($option['value'] == $local_option) : echo '<span class="dashicons dashicons-yes"></span>';
                                                    else : echo '<span class="dashicons dashicons-no"></span>';
                                                    endif; ?>
                                                </td>

                                            </tr>
                                            <?php if ((is_array($local_option)) or (is_array($option['value']))):?>
      <tr>
         <td style="word-break: break-all; ">        <?php  echo json_encode($local_option); ?></td>
         <td style="word-break: break-all; ">                <?php  echo json_encode($option['value']); ?></td>
     </tr>
                                            <?php endif;?>
                                        <?php endforeach; ?>
                                    </tbody>

                                    <tfoot>

                                    </tfoot>

                                </table>
                            </div>
                            <div class="postbox-container">
                                    <table class="widefat">
                                    <thead>
                                        <tr>
                                           <td><h3><?php _e( "Debug & System", 'wpai' ); ?> </h3></td> 
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php echo debug_info_version_check(); ?>
                                        </tbody>
                                                </table>
                            </div>
                   </div>

                           
                        </div>
                    </div>
                <?php
            }

            function send_logs()
            {
                $email = get_option('admin_email');
                $to = 'wpai@3ele.de';
                $message = debug_info_version_check();
                $subject = 'Installer Log from setup:';
                $headers = 'From: ' . $email . "\r\n" .
                    'Reply-To: ' . $email . "\r\n";
                $attachments = plugin_dir_path(__FILE__) . '/local_setup.json';
                $sent =  wp_mail($to, $subject, $message, $headers, $attachments);
                if ($sent == True) { 
            }
            }