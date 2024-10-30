<?php

global $wpdb;


////////////////////////////////////////////////////////////////////////////////
// print plugin option page and check post data
////////////////////////////////////////////////////////////////////////////////
function jbf_save_plugin_options()
{
  $options = array(
//       'jbf_import_user_id'           => $_POST['jbf_import_user_id'],
//       'jbf_import_feed_url'          => $_POST['jbf_import_feed_url'],
       'jbf_jv_profit_center_id'      => $_POST['jbf_jv_profit_center_id'],
       'jbf_import_cats'              => $_POST['jbf_import_cats_select'],
       'jbf_post_header_text'         => $_POST['jbf_post_header_text'],
       'jbf_post_footer_text'         => $_POST['jbf_post_footer_text'],
       'jbf_count_post_first_import'  => intval($_POST['jbf_count_post_first_import']),
//       'jbf_count_post_next_imports'  => $_POST['jbf_count_post_nect_import'],
       'jbf_import_schedule'          => $_POST['jbf_import_schedule'],
       'jbf_publish_option'           => $_POST['jbf_publish_option']
        );

  jbf_update_options($options);

  //  set cron event
  wp_clear_scheduled_hook('scheduled_import_article_hook');
  $schedule_interval = jbf_get_option('jbf_import_schedule');
  $no_of_days=1;
  if($schedule_interval=='weekly')
    $no_of_days=7;
  else if($schedule_interval=='monthly')
    $no_of_days=30;
        
  wp_schedule_event( time()+( $no_of_days*24*60*60), $schedule_interval, 'scheduled_import_article_hook' );

  return;
}



////////////////////////////////////////////////////////////////////////////////
// import articles from import_feed_url
////////////////////////////////////////////////////////////////////////////////
function jbf_fetch_articles()
{
  $url = jbf_get_option('jbf_import_feed_url');

  jbf_save_plugin_options();

  $blog_user_for_import = jbf_get_option('jbf_import_user_id');
  $jv_profit_center_id  = jbf_get_option('jbf_jv_profit_center_id');
    
  $no_of_imported_posts = jbf_importArticles( $url, $jv_profit_center_id, $blog_user_for_import, jbf_get_option('jbf_count_post_first_import') );
    
  $url_display = substr( $url, strpos( $url, 'http://') + 7, (strpos( $url, '/', (strpos($url, 'http://') + 8)) - (strpos($url, 'http://') + 7)) );
  if ( $no_of_imported_posts == 0 )
  {
    echo '<div id="message" class="updated fade">';
    echo '<strong>Successfully imported from ' . $url_display . ', but didn\'t found any new posts.</strong></div>';
  }
  else if ( $no_of_imported_posts > 0 )
  {
    echo '<div id="message" class="updated fade">';
    echo '<strong>Successfully imported ' . $no_of_imported_posts . ' posts from ' . $url_display . '.</strong></div>';
  }

  return;    
}



////////////////////////////////////////////////////////////////////////////////
// import articles from import_feed_url
////////////////////////////////////////////////////////////////////////////////
function jbf_importArticles($url,$jv_profit_center_id,$blog_user_id,$no_of_article_to_be_imported)
{
  global $wpdb;

  $no_of_imported_posts=0;

  if( trim($jv_profit_center_id) == "" )
  {
    echo "Invalid JV Profit Center affiliate ID. Please re-enter or create a valid ID at http://www.jvprofitcenter.com.";
    return -1;
  }
  else if($jv_profit_center_id != "")
  {
    $url=$url."?userid=".$jv_profit_center_id;

    $rss = new jbf_lastRSS();  
    $rss_content = $rss->Get($url);
    $items = $rss_content['items'];
    $i=0;
    $no_of_imported_posts=0;

    if($no_of_article_to_be_imported > count($items))
      $no_of_article_to_be_imported = count($items);
    //while ($i<count($items)){
    while ($i<$no_of_article_to_be_imported)
    {
      $postTitle=$items[$i]['title'];
      $postContent=$items[$i]['summary'];
        
      $postTitle=str_replace(']]&gt;','',str_replace('&lt;![CDATA[','',$postTitle));
//      $postTitle=str_replace(']]>','',str_replace('<![CDATA[','',$postTitle));
      $postContent=str_replace(']]&gt;','',str_replace('&lt;![CDATA[','',$postContent));

      if($items[$i]['is_error_msg']==1)
      {
        echo $postContent;
        return -1;
      }
        
      $postDate = $items[$i]['published'];
      $postTitle = jbf_custom_htmlspecialchars_decode($postTitle);
      $postContent = jbf_custom_htmlspecialchars_decode($postContent);
        
      $my_post = array();
      $my_post['post_title'] = $postTitle;
      $my_post['post_content'] = '<p>' . jbf_get_option('jbf_post_header_text') . '</p>' . $postContent . '<p>' . jbf_get_option('jbf_post_footer_text') . '</p>';
      $my_post['post_status'] = jbf_get_option("jbf_publish_option");
      $my_post['post_author'] = $blog_user_id;
      $my_post['post_date'] =$postDate;
      $my_post['post_date_gmt'] = $postDate;
      $my_post['post_modified'] = $postDate;
      $my_post['post_modified_gmt'] = $postDate;
      $all_tags_for_a_post=$items[$i]['post_tags'];
      $tags_array=explode(",",$all_tags_for_a_post);
      $post_tag_id=array();
      if( !jbf_postExists($postTitle) )
      {
                //////////////////////////////////
        remove_filter('content_save_pre', 'wp_filter_post_kses');
        remove_filter('excerpt_save_pre', 'wp_filter_post_kses');
        remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');
                /////////////////////////////////
        $post_id=wp_insert_post( $my_post );

        $cat_ids = jbf_get_option('jbf_import_cats');
//        $cat_ids=explode(",", jbf_get_option('jbf_import_cats'));
                    
        $tepm_post_id=$post_id;
        wp_set_post_categories($post_id, $cat_ids);
        foreach($tags_array as $a_tag)
        {
          if ( '' == $a_tag )
            continue;
          $a_slug = sanitize_term_field('slug', $a_tag, 0, 'post_tag', 'db');
          $a_tag_obj = get_term_by('slug', $a_slug, 'post_tag');
          $a_tag_id=0;
          if ( ! empty($a_tag_obj) )
            $a_tag_id = $a_tag_obj->term_id;
          if($a_tag_id==0)
          {
            $a_tag=$wpdb->escape($a_tag);
            $a_tag_id = wp_insert_term($a_tag, 'post_tag');
            if ( is_wp_error($a_tag_id) )
              continue;
            $a_tag_id = $a_tag_id['term_id'];
          }
          $post_tag_id[]=intval($a_tag_id);
        }
        wp_set_post_tags($tepm_post_id,$post_tag_id);
        $no_of_imported_posts++;
        //$mytags=array('good','bad','ugly');
        //wp_set_post_tags($tepm_post_id,$mytags);
      }
      $i++;
    }
  }

  return  $no_of_imported_posts;
}
    
function jbf_postExists($postTitle)
{
  $retValue=false;
  global $wpdb;

  $table=$wpdb->prefix."posts";
  $result=mysql_query("select * from $table where post_title='".$postTitle."'");
    
  if(mysql_num_rows($result)>0)
    $retValue=true;
 
  return $retValue;
}
    
function jbf_custom_htmlspecialchars_decode($str, $options="") 
{
  $trans = get_html_translation_table(HTML_SPECIALCHARS);
  //$trans = get_html_translation_table(HTML_SPECIALCHARS, $options);
  $decode = ARRAY();

  foreach ($trans AS $char=>$entity) 
  {
    $decode[$entity] = $char;
  }
  $str = strtr($str, $decode);

  return $str;
}



















?>