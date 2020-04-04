<?php

/**
 * Plugin Name:     WP AutoInstaller
 * Plugin URI:      https://wpai.3ele.de
 * Description:     Install from local Setup
 * Author:          Sebastian Weiss
 * Author URI:      wpai.3ele.de
 * Text Domain:     WP Auto Installer
 * Domain Path:     /wpai
 * Version:         0.1.0
 *
 */

// Your code starts here.

define( 'PLUGIN_DIR', dirname(__FILE__).'/' );  
require_once(PLUGIN_DIR. '/classes/wpai.php');
require_once(PLUGIN_DIR. '/admin/wpai-interface.php');
add_action('wp_enqueue_scripts','pretty_json_init');
// https://github.com/warfares/pretty-json
function pretty_json_init() {
    wp_enqueue_script( 'pretty-json-js', plugins_url( 'http://warfares.github.io/pretty-json/pretty-json-min.js', __FILE__ ));
}





function is_json($string, $return_data = false)
{
    $data = json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE) ? ($return_data ? $data : TRUE) : FALSE;
}

function wpai_update_option_init(){
    
    
    //$awpi->eleAutomatics_deactivate_plugins();
if (get_option('wpai_import_options')){
    $awpi = new AutoWPInstance();
    $awpi->eleAutomatics_do_custom_options();
};

 //   $awpi->wpai_change_content();
    }

add_action( 'init', 'wpai_update_option_init' );



function gimme_your_options($option_name)
{
    $blame = 'core';
    $debug_backtrace = debug_backtrace();
    foreach ($debug_backtrace as $call) {
        if (empty($call['file']))
            continue;

        if (!preg_match('#wp-content/((?:(?:mu-)?plugins|themes)/.+)#i', $call['file'], $matches))
            continue;

        $blame = $matches[1];
        break;
    }
    $options_log = $option_name.',';
    
 //   file_put_contents ( plugin_dir_path(__FILE__) . '/action.logs' ,$options_log, FILE_APPEND | LOCK_EX );
   
}

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
    $debug_infos = '';
    $debug_infos .= '<tr><td>' . __('WordPress Version: ', 'debug-info') . '</td><td>' . $wp . '</td></tr>';
    $debug_infos .= '<tr><td>' . __('Current WordPress Theme: ', 'debug-info') . '</td><td>' . $themeversion . '</td></tr>';
    $debug_infos .= '<tr><td>' . __('Theme Author: ', 'debug-info') . '</td><td>' . $themeauth .  '</td></tr>';
    $debug_infos .= '<tr><td>' . __('Theme URI: ', 'debug-info') . '</td><td>' . $uri . '</td></tr>';
    $debug_infos .= '<tr><td>' . __('PHP Version: ', 'debug-info') . '</td><td>' . $php . '</td></tr>';
    $debug_infos .= '<tr><td>' . __('MySQL Version: ', 'debug-info') . '</td><td>' . $mysql . '</td></tr>';
    $debug_infos .= '<tr><td>' . __('Apache Version: ', 'debug-info') . '</td><td>' . $apache . '</td></tr>';
    return $debug_infos;
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
    return True;
}



function delete_mu_plugin()
{
    $path = WP_CONTENT_DIR . '/mu-plugins/';

    if (file_exists($path)) {
        if(rrmdir($path)){
            $wpai_options = get_option('wpai_options');
            $wpai_options['mu_plugin'] = 0;
            update_option('wpai_options', $wpai_options);
        }
    }
}

function wpai_check_mu_plugin(){
    $path = WP_CONTENT_DIR . '/mu-plugins/';
    if (file_exists($path)) {  return True;  } else {  return False;  
    }
}  
function wpai_activate(){
     if (!get_option('wpai_options')) {
        $wpai_options = [];
        add_option('wpai_import_options', true, 'yes');
        if (wpai_check_mu_plugin()){
            $wpai_options['mu_plugin'] = 1;
            
        }
        else {
            $wpai_options['mu_plugin'] = 0; 
        }                   
    add_option('wpai_options', $wpai_options, 'yes');
                }
                else {
                    add_option('wpai_import_options', false, 'yes');
                    $wpai_options = get_option('wpai_options');
                    if (wpai_check_mu_plugin()){
                        $wpai_options['mu_plugin'] = 1;
                    }
                    else {
                        $wpai_options['mu_plugin'] = 0; 
                    }
    update_option('wpai_options', $wpai_options, 'yes');  
                }


            } 

add_action( 'admin_init', 'wpai_activate' );                     
 
        

add_action('add_option', 'gimme_your_options');
add_action('update_option', 'gimme_your_options'); 