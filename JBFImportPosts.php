<?php
/*
Plugin Name: JBF Import Posts 
Plugin URI: http://jonbensonfitness.com/wp-plugin
Description: Import posts/articles from John Benson Fitness&copy; Host Blog to your blog via last rss feed. WP Cron settings for automatical import in regular intervals.
Version: 2.2.5
Author: John Benson
Author URI: http://jonbensonfitness.com
License: GPL2
*/

global $wpdb;

define('JBFPURL', WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) );
define('JBFPDIR', WP_PLUGIN_DIR . '/' . str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) );


// relative path to WP_PLUGIN_DIR where the translation files will sit:
$plugin_path = plugin_basename( dirname( __FILE__ ) .'/lang' );
load_plugin_textdomain( 'jbf_import_posts', '', $plugin_path );

include_once( dirname(__FILE__) . '/JBFImportPosts-lastRSS.php');
include_once( dirname(__FILE__) . '/JBFImportPosts-functions.php');
include_once( dirname(__FILE__) . '/JBFImportPosts-option-page.php');

register_activation_hook( __FILE__, 'jbf_plugin_activation');   
register_deactivation_hook( __FILE__, 'jbf_plugin_deactivation'); 

add_action( 'admin_menu', 'jbf_plugin_add_option_page');
add_action( 'admin_head', 'jbf_plugin_load_header_tags');
add_filter( 'cron_schedules', 'jbf_more_reccurences');
add_action( 'scheduled_import_article_hook', 'jbf_import_articles' );





////////////////////////////////////////////////////////////////////////////////
// plugin activation hook
////////////////////////////////////////////////////////////////////////////////
function jbf_plugin_activation() 
{
  //  check if default cat exists
  if ( !get_cat_ID('ImportFit') ) 
  {
    wp_create_category( 'ImportFit' );
  }

  add_option( 'jbf_import_posts', array(), '', 'no');
  jbf_set_option_defaults();

  jbf_migrate_old_options();

  return; 
}

////////////////////////////////////////////////////////////////////////////////
// plugin deactivation hook
////////////////////////////////////////////////////////////////////////////////
function jbf_plugin_deactivation() 
{
  wp_clear_scheduled_hook('scheduled_import_article_hook');

  delete_option('jbf_import_posts');
}

////////////////////////////////////////////////////////////////////////////////
// add plugin option page
////////////////////////////////////////////////////////////////////////////////
function jbf_plugin_add_option_page()
{
  add_menu_page( 'JBF Import Posts', 'JBF Import Posts', 5, __FILE__, 'jbf_plugin_create_option_page');
  add_submenu_page(__FILE__, 'About', 'About', 5, 'sub-page', 'display_fit365Online_plugin_about');
  add_submenu_page(__FILE__, 'JV Profit Center', 'JV Profit Center', 5, 'sub-page2', 'javascript_to_redirect_to_jvprofitcenter');
//  add_options_page('JBF Import Posts', 'Import fit365Online', 8, __FILE__, 'jbf_create_option_page');
}

////////////////////////////////////////////////////////////////////////////////
// load plugin wp-admin css and js
////////////////////////////////////////////////////////////////////////////////
function jbf_plugin_load_header_tags()
{
  $js_number_categories = get_categories(array('hide_empty' => false));
  $js_category_count=0;
  foreach ($js_number_categories as $category) 
    $js_category_count++;

  echo 	"\n\n";
  echo 	'<!-- JBF Import Posts - Plugin Option CSS -->' . "\n";
  echo 	'<link rel="stylesheet" type="text/css" media="all" href="' . JBFPURL . 'css/plugin-option.css" />';
/*

  $data = get_plugin_data(__FILE__);
//  wp_enqueue_script( 'get_output', plugin_dir_url( __FILE__ ) . 'js/ajax/get_output.js', array( 'jquery', 'json2' ), "1.0.30", true );
  wp_enqueue_script( 'jbf_script', plugin_dir_url( __FILE__ ) . 'js/jbf_import_posts.js', array('jquery'), false, false);
  wp_register_script( 'jbf_script', plugin_dir_url( __FILE__ ) . 'js/jbf_import_posts.js', array('jquery'), false, false);
  wp_register_style( 'jbf_style', JBFPURL . 'css/plugin-option.css', false, false);
//  wp_register_script( 'jbf_script', JBFPURL . 'js/jbf_import_posts.js', array('jquery'), $data['Version']);
//  wp_register_style( 'jbf_style', JBFPURL . 'css/plugin-option.css', array(), $data['Version']);
  wp_enqueue_script('jbf_script');
  wp_enqueue_style( 'jbf_style');
*/
	
  return;
}


////////////////////////////////////////////////////////////////////////////////
// plugin options functions
////////////////////////////////////////////////////////////////////////////////
function jbf_get_option($field) 
{
  if (!$options = wp_cache_get('jbf_import_posts')) 
  {
    $options = get_option('jbf_import_posts');
    wp_cache_set('jbf_import_posts',$options);
  }
  return $options[$field];
}

function jbf_update_option($field, $value) 
{
  jbf_update_options(array($field => $value));
}

function jbf_update_options($data) 
{
  $options = array_merge(get_option('jbf_import_posts'),$data);
  update_option('jbf_import_posts',$options);
  wp_cache_set('jbf_import_posts',$options);
}

function jbf_migrate_old_options() 
{
  global $wpdb;

  //  check for a old Option
  if (get_option('fit365online_import_schedule') === false) 
  {
    return;
  }

  $old_fields = array(
       '0' => 'blog_user_for_import',
       '1' => 'fit365noline_feed_url',
       '2' => 'jv_profit_center_id',
       '3' => 'import_from_feed365Online_under_this_category',
       '4' => 'disclaimer_prefix_for_fit365_online',
       '6' => 'fit365online_number_of_article_for_first_import',
       '7' => 'fit365online_number_of_article_for_subsequent_import',
       '8' => 'fit365online_import_schedule',
       '9' => 'fit365online_import_as_option'
       );

  $new_fields = array(
       '0' => 'jbf_import_user_id',
       '1' => 'jbf_import_feed_url',
       '2' => 'jbf_jv_profit_center_id',
       '3' => 'jbf_import_cats',
       '4' => 'jbf_post_header_text',
       '5' => 'jbf_post_footer_text',
       '6' => 'jbf_count_post_first_import',
       '7' => 'jbf_count_post_next_imports',
       '8' => 'jbf_import_schedule',
       '9' => 'jbf_publish_option'
       );

  foreach($old_fields as $index=>$field) 
  {
    if ( $index == 3 )
    {
      $cats = get_option($old_fields[$index]);
      if ( is_array($cats) )
        jbf_update_option($new_fields[$index], $cats);
      else
        jbf_update_option($new_fields[$index], array($cats));
    }
    else
      jbf_update_option($new_fields[$index], get_option($old_fields[$index]));
    delete_option($old_fields[$index]);
  }
  $wpdb->query("OPTIMIZE TABLE `" . $wpdb->options . "`");

  return;
}

function jbf_set_option_defaults()
{
  $current_user_id=1;
  global $current_user;    
  get_currentuserinfo();

  if ( $current_user->ID != '' ) 
    $current_user_id=$current_user->ID;

  $importfit_cat = intval(get_cat_ID('ImportFit'));
 
  $default_options = array(
       'jbf_import_user_id'           => $current_user_id,
       'jbf_import_feed_url'          => 'http://fit365online.com/rss_aff_for_jvpc.php',
       'jbf_jv_profit_center_id'      => '',
       'jbf_import_cats'              => array($importfit_cat),
       'jbf_post_header_text'         => '[ Note: This article was written by fitness and nutrition author Jon Benson. I have his permission to share it with you. ]',
       'jbf_post_footer_text'         => '[ Thank you for reading. If you are intrested in more informations please contact us or subscribe to our blog feed and newsletter. ]',
       'jbf_count_post_first_import'  => 1,
       'jbf_count_post_next_imports'  => 1,
       'jbf_import_schedule'          => 'Daily',
       'jbf_publish_option'           => 'publish'
        );

  $jbf_options = get_option('jbf_import_posts');

  foreach ($default_options as $def_option => $value )
  {
    if ( !$jbf_options[$def_option] )
    {
      jbf_update_option( $def_option, $value );
    }
  }

  return;
}




////////////////////////////////////////////////////////////////////////////////
// print plugin option page and check post data
////////////////////////////////////////////////////////////////////////////////
function jbf_plugin_create_option_page()
{
  if ( $_POST['jbf_update_options_btn'] )
  {
    jbf_save_plugin_options();

    echo '<div id="message" class="updated fade">';
    echo '<strong>Plugin Settings saved !!!</strong>.</div>';
  }

  if ( $_POST['jbf_import_btn'] )
  {
    jbf_fetch_articles();
  }

  jbf_plugin_print_option_page();

  return;
}



function jbf_is_min_wp($version) 
{
  return version_compare( $GLOBALS['wp_version'], $version. 'alpha', '>=');
}



function jbf_display_fit365Online_plugin_about()
{
?>
<script language="javascript" type="text/javascript">
window.open('http://www.jvprofitcenter.com/blog/?p=137', '_blank', 'toolbar=0,location=0,menubar=0');
</script>
<?php
}

function jbf_javascript_to_redirect_to_jvprofitcenter()
{
?>
<script language="javascript" type="text/javascript">
window.open('http://www.jvprofitcenter.com/', '_blank', 'toolbar=0,location=0,menubar=0');
</script>
<?php
}

function jbf_import_articles()
{
  $blog_user_for_import=jbf_get_option('jbf_import_user_id');
  jbf_importArticles( jbf_get_option('jbf_import_feed_url'), jbf_get_option('jbf_jv_profit_center_id'), $blog_user_for_import, jbf_get_option('jbf_count_post_first_import'));
}

function jbf_more_reccurences() 
{
    return array(
        'weekly' => array('interval' => 604800, 'display' => 'Once Weekly'),
        'monthly' => array('interval' => 2592000, 'display' => 'Once Monthly'),
        );
}


?>