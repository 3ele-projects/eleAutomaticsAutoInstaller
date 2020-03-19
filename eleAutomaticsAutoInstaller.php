<?php

/**
 * Plugin Name:     EleAutomaticsAutoInstaller
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          YOUR NAME HERE
 * Author URI:      YOUR SITE HERE
 * Text Domain:     eleAutomaticsAutoInstaller
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         EleAutomaticsAutoInstaller
 */

// Your code starts here.

function debug_info_version_check()
{
    //outputs basic information
    $notavailable = __('This information is not available.', 'debug-info');
    if (!function_exists('get_bloginfo')) {
        $wp = $notavailable;
    } else {
        $wp = get_bloginfo('version');
    }

    if (!function_exists('wp_get_theme')) {
        $theme = $notavailable;
    } else {
        $theme = wp_get_theme();
    }

    if (!function_exists('get_plugins')) {
        $plugins = $notavailable;
    } else {
        $plugins_list = get_plugins();
        if (is_array($plugins_list)) {
            $active_plugins = '';
            $plugins = '<ul>';
            foreach ($plugins_list as $plugin) {
                $version = '' != $plugin['Version'] ? $plugin['Version'] : __('Unversioned', 'debug-info');
                if (!empty($plugin['PluginURI'])) {
                    $plugins .= '<li><a href="' . $plugin['PluginURI'] . '">' . $plugin['Name'] . '</a> (' . $version . ')</li>';
                } else {
                    $plugins .= '<li>' . $plugin['Name'] . ' (' . $version . ')</li>';
                }
            }
            $plugins .= '</ul>';
        }
    }

    if (!function_exists('phpversion')) {
        $php = $notavailable;
    } else {
        $php = phpversion();
    }

    if (!function_exists('debug_info_get_mysql_version')) {
        $mysql = $notavailable;
    } else {
        $mysql = debug_info_get_mysql_version();
    }

    if (!function_exists('apache_get_version')) {
        $apache = $notavailable;
    } else {
        $apache = apache_get_version();
    }

    $themeversion    = $theme->get('Name') . __(' version ', 'debug-info') . $theme->get('Version') . $theme->get('Template');
    $themeauth        = $theme->get('Author') . ' - ' . $theme->get('AuthorURI');
    $uri            = $theme->get('ThemeURI');

    echo '<strong>' . __('WordPress Version: ', 'debug-info') . '</strong>' . $wp . '<br />';
    echo '<strong>' . __('Current WordPress Theme: ', 'debug-info') . '</strong>' . $themeversion . '<br />';
    echo '<strong>' . __('Theme Author: ', 'debug-info') . '</strong>' . $themeauth . '<br />';
    echo '<strong>' . __('Theme URI: ', 'debug-info') . '</strong>' . $uri . '<br />';
    echo '<strong>' . __('PHP Version: ', 'debug-info') . '</strong>' . $php . '<br />';
    echo '<strong>' . __('MySQL Version: ', 'debug-info') . '</strong>' . $mysql . '<br />';
    echo '<strong>' . __('Apache Version: ', 'debug-info') . '</strong>' . $apache . '<br />';
    echo '<strong>' . __('Active Plugins: ', 'debug-info') . '</strong>' . $plugins . '<br />';
}

/* Skizze LogfilesW writer */


function _log($model, $action, $source)
{
    $date = new DateTime();
    $timestamp = $date->format("y:m:d h:i:s");
    $log = $timestamp . ',' . $model . ',' . $action . ',' . $source . "\n";
    file_put_contents(plugin_dir_path(__FILE__) . 'logs/installer.csv', $log, FILE_APPEND);
}

add_action('admin_menu', 'test_plugin_setup_menu');

function test_plugin_setup_menu()
{
    add_menu_page('Lightweb Media', 'Lightweb Media', 'manage_options', 'test-plugin', 'lightweb_media_init');
}
function rrmdir($src)
{
    $dir = opendir($src);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            $full = $src . '/' . $file;
            if (is_dir($full)) {
                rrmdir($full);
            } else {
                unlink($full);
            }
        }
    }
    closedir($dir);
    rmdir($src);
}
function send_logs()
{
    $email = 'test@test.de';
    $to = 'agentur@3ele.de';
    $message = 'agentur@3ele.de';
    $subject = 'test';
    $headers = 'From: ' . $email . "\r\n" .
        'Reply-To: ' . $email . "\r\n";

    $attachments = plugin_dir_path(__FILE__).'/logs/installer.csv';
    $sent =  wp_mail($to, $subject, $message, $headers);
}
function delete_mu_plugin()
{
    $path = WP_CONTENT_DIR . '/mu-plugins/';

    if (file_exists($path)) {
        rrmdir($path);
    }
}

function lightweb_media_init()
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
?>
    <div class="wrap">
        <div id="welcome-panel" class="welcome-panel">
            <div class="welcome-panel-content">
                <h2>Willkommen bei WordPress, powered by lightweb Media!</h2>
                <p class="about-description">Wir haben einige Links zusammengestellt, um dir den Start zu erleichtern:</p>
                <div class="welcome-panel-column-container">
                    <div class="welcome-panel-column">
                        <h3>Jetzt loslegen</h3>
                    </div>
                    <div class="welcome-panel-column">
                        <h3>Nächste Schritte</h3>
                        <ul>
                            <li>
                                <?php echo '<form action="options-general.php?page=test-plugin" method="post">';
                                wp_nonce_field('delete_mu-plugin');
                                echo '<input type="hidden" value="true" name="delete_mu-plugin" />';
                                submit_button('Delete Installer');
                                echo '</form>';
                                ?>
                            </li>
                        </ul>
                    </div>
                    <div class="welcome-panel-column welcome-panel-last">
                        <h3>Do you need help?</h3>
                        <ul>
                            <li>
                                <?php echo '<form action="options-general.php?page=test-plugin" method="post">';

                                // this is a WordPress security feature - see: https://codex.wordpress.org/WordPress_Nonces
                                wp_nonce_field('send_logs_action');
                                echo '<input type="hidden" value="true" name="send_logs" />';
                                submit_button('Send Logs');
                                echo '</form>';
                                ?>

                            </li>

                        </ul>
                    </div>
                </div>
            </div>
            <div id="dashboard-widgets" class="metabox-holder">
                <div class="welcome-panel-content">
 <h2 class="nav-tab-wrapper">
                        <a href="?page=test-plugin&tab=display_options" class="nav-tab nav-tab-active">Logs & Server</a>
                        <!-- <a href="?page=test-plugin&tab=social_options" class="nav-tab">Manual</a>
                            <a href="?page=test-plugin&tab=account" class="nav-tab">Account</a> -->
                    </h2>
                    <div>
                        <div id="dashboard-widgets" class="metabox-holder">
                            <div class="postbox-container" style="width:72%!important">
                                <table class="widefat fixed" cellspacing="0">
                                <thead>
                                                <tr>
                                                    <th  class="manage-column column-cb check-column" scope="col">model</th>
                                                    <th  class="manage-column column-cb check-column" scope="col">action</th>
                                                    <th  class="manage-column column-cb check-column" scope="col">source</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                    <?php
                                    $row = 1;
                                    $log_file = plugin_dir_path(__FILE__) . 'logs/installer.csv';
                                    $Data = array_map('str_getcsv', file($log_file));
                                    $i = 0;
                                    foreach ($Data as $row) :  ?>
                                                <tr class="alternate">
                                                    <td class="column-columnname"><?php echo $row[0] ?></td>
                                                    <td class="column-columnname"><?php echo $row[1] ?></td>
                                                    <td class="column-columnname"><?php echo $row[2] ?></td>
                                                    <td class="column-columnname"><?php echo $row[3] ?></td>
                                                </tr>                                     
                                        <?php $i++;
                                    endforeach;
                                        ?>
                                            </tbody>
                                </table>
                                <?php
                                echo '<form action="options-general.php?page=test-plugin" method="post">';
                                wp_nonce_field('send_logs_action');
                                echo '<input type="hidden" value="true" name="send_logs" />';
                                submit_button('Send Logs');
                                echo '</form>';
                                echo '</div>'; ?>
                            </div>
                            <div class="postbox-container" style="width:28%!important">
  <div class="widget-control-actions">

                                    <div class="alignright">
                                        <?php debug_info_version_check(); ?>
                                    </div>
                                    <br class="clear">
                                </div>

                            </div>
                        </div>
                    </div>
                <?php
            }

function eleAutmaticsAutoCreater_activate(){
                if(!get_option('eleAutomaticInit')){
                 #   add_option( 'eleAutomaticInit', '0', '', 'yes' );
                 #   wp_redirect(admin_url('/wp-admin/admin.php?page=test-plugin', 'http'), 301);
                    add_option( 'eleAutomaticInit', '1', '', 'yes' );
                  
                }
           
              
            } 
            

            
            
            
add_action( 'admin_init', 'eleAutmaticsAutoCreater_activate' );            
            ?>