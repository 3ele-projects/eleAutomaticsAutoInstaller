<?php
/**
 * Plugin Name: Your Plugin Name Here
 * Description: Short description of your plugin here.
 * Author:      your name here
 * License:     GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Basic security, prevents file from being loaded directly.
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

/* Prefix your custom functions!
 *
 * Function names must be unique in PHP.
 * In order to make sure the name of your function does not
 * exist anywhere else in WordPress, or in other plugins,
 * give your function a unique custom prefix.
 * Example prefix: wpr20151231
 * Example function name: wpr20151231__do_something
 *
 * For the rest of your function name after the prefix,
 * make sure it is as brief and descriptive as possible.
 * When in doubt, do not fear a longer function name if it
 * is going to remind you at once of what the function does.
 * Imagine you’ll be reading your own code in some years, so
 * treat your future self with descriptive naming. ;)
 */

/**
 * Pass your custom function to the wp_rocket_loaded action hook.
 *
 * Note: wp_rocket_loaded itself is hooked into WordPress’ own
 * plugins_loaded hook.
 * Depending what kind of functionality your custom plugin
 * should implement, you can/should hook your function(s) into
 * different action hooks, such as for example
 * init, after_setup_theme, or template_redirect.
 * 
 * Learn more about WordPress actions and filters here:
 * https://developer.wordpress.org/plugins/hooks/
 *
 * @param string 'wp_rocket_loaded'         Hook name to hook function into
 * @param string 'yourprefix__do_something' Function name to be hooked
 */


class AutoWPInstance {
    public $configdata;	
    public function __construct() {
		    if (file_exists(plugin_dir_path('wpai-admin-1.04').'/local_setup.json')):
			    $this->configdata = json_decode(file_get_contents(plugin_dir_path('wpai-admin-1.04').'/local_setup.json'), true);    
		    else:
	    $this->configdata = json_decode(file_get_contents('https://www.3ele.de/wpai/setups/32'), true);
	    endif;
        $this->configdata =  $this->configdata['setup'];


	}



function plugin_activation( $plugin ) {
    if( ! function_exists('activate_plugin') ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
is_plugin_active( $plugin ) ;
}

function eleAutomatics_deactivate_plugins() {
    
    $plugins = array (
        'akismet/akismet.php', 
        'hello.php'
    );
foreach ($plugins as $plugin) {
$this->deactivate_plugin($plugin);
}
  
}
/* activate pugins */
 function eleAutomatics_activate_plugins() {

    $plugins = $this->configdata;
    foreach ($plugins['plugins']  as $plugin) {

        $this->plugin_activation( $plugin['path'].'/'.$plugin['file']);
   
    }
 }
    /* deactivate plugin & delete plugin */
function deactivate_plugin($plugin) {      
            if ( is_plugin_active($plugin) ) {
                deactivate_plugins($plugin);  
            }
            delete_plugins($plugin);  
       
}

/* options */

function add_custom_option( $option ) {
    
    if( ! function_exists('add_option') ) {
        require_once ABSPATH . 'wp-admin/includes/option.php';
    }

    if(get_option($option ['key'])){
        print_r($option ['value']);
        if (is_array($option ['value'])) {
     
                                          
            }
         else {
            update_option($option ['key'], $option['value']);
        }
        
   }
   else {
    add_option($option ['key'], $option['value']);
   }

}

function wpai_download_plugin($plugin) {
$this->$plugin = $plugin;
print_r($this->$plugin['download_url']);

$url = $this->$plugin['download_url'];
$zip_file = WP_PLUGIN_DIR .'/'.$this->$plugin['path'].'.zip';
$f = file_put_contents($zip_file, fopen($url, 'r'), LOCK_EX);
if(FALSE === $f)
    die("Couldn't write to file.");
$zip = new ZipArchive;
$res = $zip->open($this->$plugin['path'].'.zip');
if ($res === TRUE) {
  $zip->extractTo($this->$plugin['path']);
  $zip->close();
  //
} else {
  print_r($url );
}
}

function eleAutomatics_do_custom_options() {
    $options  = $this->configdata;
    foreach ($options['options']  as $option) {
      if (is_array($option ['value'])) {
        $serialised = get_option( $option ['key'] );
        $data = maybe_unserialize( $serialised );
             foreach  ($option['value'] as $sub=>$value)    {
                $data[$sub] =  $value;    
                       }
            update_option($option ['key'], $data);                         
          } else {
            update_option($option ['key'], $option['value']);     
          }

    
        }
}

/* themes */

/* activate themes */
function eleAutomatics_switch_theme() {
    $themes  = $this->configdata;
    foreach ($themes['themes']  as $theme) {
        if ($theme['status'] == 'active'){
            switch_theme($theme['name']);
        }
}
}
/* Content */
function wpai_change_content() {
    /* delete sample post */
    wp_delete_post(1, true);
        /* change title from  sample page*/
    $impressum = get_post(2);
    $impressum->post_title = 'Impressum';

    wp_update_post($impressum, $impressum);  
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

function create_local_setup_json() { 
    $local_plugins=[];
    $plugins = get_plugins();
    $keys = array_keys($plugins); 
    foreach ($keys as $plugin) {
        $local_plugin = [];
        $plugin_key = explode("/",$plugin);
        $local_plugin['name']= $plugins[$plugin]['Name'];
        $local_plugin['version']= $plugins[$plugin]['Version'];
        $local_plugin['path']= $plugin_key[0];
        $local_plugin['file']= $plugin_key[1];
        $local_plugins[]= $local_plugin; 
    }


/*     $local_themes=[];
    $themes = wp_get_themes();
     print_r($themes);
    $keys = array_keys($themes); 
    foreach ($themes as $theme) {
        $local_theme = [];
        print_r($theme);
    break;
        $theme_key = explode("/",$theme);
        $local_theme['name']= $theme['headers:WP_Theme:private']['Name'];
        $local_theme['version']= $theme['headers:WP_Theme:private']['Version'];


        $local_themes .= $local_theme; 
    } */
 
    $local_options=[];

    $wpai_options = $this->configdata['options'];
    
    foreach ($wpai_options as $option) {
        $local_option = [];

        $local_option['key']= $option['key'];
        $local_option['value']= get_option($option['key']);
        $local_options[] = $local_option; 
       
    }
    $local_setup = [];
    $local_setup['setup']['options'] = $local_options;
    $local_setup['setup']['plugins'] = $local_plugins;

    file_put_contents (PLUGIN_DIR. '/local_setup.json' ,json_encode($local_setup, JSON_PRETTY_PRINT) | LOCK_EX );
}
}        