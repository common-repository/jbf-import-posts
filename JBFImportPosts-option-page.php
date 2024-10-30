<?php

function jbf_plugin_print_option_page()
{
  $options = get_option('jbf_import_posts');

  $jbf_import_user_id  = jbf_get_option('jbf_import_user_id');
  $jbf_import_feed_url = jbf_get_option('jbf_import_feed_url');
  $jbf_jv_profit_center_id = jbf_get_option('jbf_jv_profit_center_id');
  $jbf_import_cats         = jbf_get_option('jbf_import_cats');
  $jbf_post_header_text    = jbf_get_option('jbf_post_header_text');
  $jbf_post_footer_text    = jbf_get_option('jbf_post_footer_text');
  $jbf_count_post_first_import = jbf_get_option('jbf_count_post_first_import');
  $jbf_count_post_next_imports = jbf_get_option('jbf_count_post_next_imports');
  $jbf_import_schedule = jbf_get_option('jbf_import_schedule');
  $jbf_publish_option  = jbf_get_option('jbf_publish_option');

  $arr_frequency=array(
                    'daily' => 'Daily',
                    'weekly' => 'Weekly',
                    'monthly' => 'Monthly'
                      );

  $arr_import_as_options=array(
                    'publish' => 'Publish Immediately (Best Option)',
                    'draft'   => 'Save As Drafts'
                              );

  //  check if default cat exists
  if ( !get_cat_ID('ImportFit') ) 
  {
    wp_create_category( 'ImportFit' );
  }

  $categories = get_categories(array('hide_empty' => false));

  echo
  '<div class="wrap">' . "\n" .
  '  <div class="icon32"></div>' . "\n" .
  '  <h2>JBF Import Posts - Plugin Settings</h2>' . "\n" .
  '  <hr />' . "\n" .
  '  <p>' . "\n" .
  '    <b>Welcome to Jon Benson\'s WP Tool!</b><br />' . "\n" .
  '    This free plugin allows you to import articles from Jon Benson\'s Fitness and nutrition blog under <a class="link-extern" href="http://www.jonbensonfitness.com" target="_blank" tilte="Jon Bensons Fitness">http://www.jonbensonfitness.com</a>, complete <b>with your affiliate links</b>.This provides you with automatic content that can also earn you commissions on all of Jon Benson\'s Clickbank Products.' . "\n" .
  '  </p>' . "\n" .
  '  <hr />' . "\n" .
  '  <div class="clear"></div>' . "\n" . 
  '  <form name="jbf_import_post_form" method="post" action="">' . "\n";

  wp_nonce_field('jbf_import_posts');

  echo
  '  <div class="metabox-holder has-right-sidebar" id="poststuff">' . "\n" .
  '    <div class="inner-sidebar float-right">' . "\n" .
  '      <div class="meta-box-sortabless ui-sortable" id="side-sortables">' . "\n" .
  '        <div class="postbox" id="jbf_info">' . "\n" .
  '          <h3 class="hndle">Jon Benson Infos</h3>' . "\n" .
  '          <div class="inside">' . "\n" .
  '            <ul>' . "\n" .
  '              <li><img class="img-link-ico" src="' . JBFPURL . 'img/jv_profit_center_favicon.jpg" alt="JV Profit Center Logo" /><a class="link-extern" href="http://www.jvprofitcenter.com" target="_blank" title="JV Profit Center">JV Profit Center</a></li>' . "\n" .
  '              <li><img class="img-link-ico" src="http://www.jonbensonfitness.com/favicon.ico" alt="JonBensonFitness.com Logo" /><a class="link-extern" href="http://www.jonbensonfitness.com" target="_blank" title="JBF Product Support Center">JBF Product Support Center</a></li>' . "\n" .
  '            </ul>' . "\n" .
  '          </div>' . "\n" .
  '        </div>' . "\n" .
  '        <div class="postbox" id="jbf_links">' . "\n" .
  '          <h3 class="hndle">Links</h3>' . "\n" .
  '          <div class="inside">' . "\n" .
  '            <ul>' . "\n" .
  '              <li><img class="img-link-ico" src="http://www.clickbank.com/favicon.ico" alt="Clickbank.com Logo" /><a class="link-extern" href="http://www.clickbank.com" target="_blank" title="Clickbank.com">Clickbank.com</a></li>' . "\n" .
  '            </ul>' . "\n" .
  '          </div>' . "\n" .
  '        </div>' . "\n" .
  '      </div>' . "\n" .
  '    </div>' . "\n" .
  '    <div class="has-sidebar jbf-padded float_left">' . "\n" .
  '      <div class="has-sidebar-content" id="post-body-content">' . "\n" .
  '        <div class="meta-box-sortabless">' . "\n" .
  '          <div class="postbox float-left" id="jbf-settings">' . "\n" .
  '            <h3>' . __('Settings', 'jbf_import_posts') . '</h3>' . "\n" .
  '            <div class="inside">' . "\n" .
  '              <b>Import from JonBensonFitness.com</b>' . "\n" .
  '              <table class="form-table">' . "\n" .
  '              <tr><th class="jbf_option_left_part"><label for="jbf_jv_profit_center_id">Enter your JV Profit Center Id</label></th>' . "\n" .
  '                  <td><input type="text" id="jbf_jv_profit_center_id" name="jbf_jv_profit_center_id" value="' . $jbf_jv_profit_center_id . '" /></td>' . "\n" .
  '              </tr>' . "\n" .
  '              <tr><th class="jbf_option_left_part"><label for="jbf_post_header_text">Edit the Default Article Header</label></th>' . "\n" .
  '                  <td><b><textarea rows="3" cols="55" id="jbf_post_header_text" name="jbf_post_header_text">' . $jbf_post_header_text . '</textarea></b></td>' . "\n" .
  '              </tr>' . "\n" .
  '              <tr><th class="jbf_option_left_part"><label for="jbf_post_footer_text">Edit the Default Article Footer</label></th>' . "\n" .
  '                  <td><b><textarea rows="3" cols="55" id="jbf_post_footer_text" name="jbf_post_footer_text">' . $jbf_post_footer_text . '</textarea></b></td>' . "\n" .
  '              </tr>' . "\n" .
  '              <tr><th class="jbf_option_left_part"><label for="">How Frequently Do You Want New Articles Imported</label></th>' . "\n" .
  '                  <td><ul><li>' . "\n";

  foreach( $arr_frequency as $intervall => $value)
  {
    if ( $jbf_import_schedule == $intervall )
      $checked = ' checked="checked" ';
    else
      $checked = ' ';
    echo '      <input type="radio" class="jbf-radio" name="jbf_import_schedule" id="jbf_import_schedule_' . $intervall . '" value="' . $intervall . '"' . $checked . ' />' . "\n";
    echo '      <label for="jbf_import_schedule_' . $intervall . '">' . $value . '</label>' . "\n";
  }

  echo
  '              </li></ul></td>' . "\n" .
  '              </tr>' . "\n" .
  '              <tr><th class="jbf_option_left_part"><label for="">Set New Articles On Import To... </label></th>' . "\n" .
  '                <td><ul><li>' . "\n";

  foreach( $arr_import_as_options as $key=>$value )
  {
    if( $jbf_publish_option == $key )
      $checked = ' checked="checked" ';
    else
      $checked = ' ';
    echo '       <input type="radio" class="jbf-radio" name="jbf_publish_option" id="jbf_publish_option_' . $key . '" value="' . $key . '"' . $checked . ' />' . "\n";
    echo '       <label for="jbf_publish_option_' . $key . '">' . $value . '</label>' . "\n";
  }

  echo
  '              </li></ul></td>' . "\n" .
  '              </tr>' . "\n" .
  '              <tr><th class="jbf_option_left_part"><label for="">Select the Categories For Imported Articles</label></th>' . "\n" .
  '                  <td><select name="jbf_import_cats_select[]" id="jbf_import_cats_select_tag" multiple="multiple" size="5">' . "\n";

  foreach( $categories as $cat)
  { 
    if ( in_array( $cat->cat_ID, $jbf_import_cats ) )
    {
      echo '  <option value="' . $cat->cat_ID . '" selected="selected">' . $cat->cat_name . '</option>' . "\n";
    }
    else
    {
      echo '  <option value="' . $cat->cat_ID . '">' . $cat->cat_name . '</option>' . "\n";
    }
  }

  echo
  '                   </select></td></tr>' . "\n" .
  '              <tr><th class="jbf_option_left_part"><label for="">Number Of Articles For First Import:</label></th>' . "\n" .
  '                  <td><select id="jbf_count_post_first_import_select" name="jbf_count_post_first_import">';

  for ( $i = 0; $i < 5; $i++ )
  {
    if ( ($i + 1) == $jbf_count_post_first_import )
      $selected = ' selected="selected" ';
    else
      $selected = ' ';
    echo '       <option value="' . ($i + 1) . '" ' . $selected . '>' . ($i + 1) . '</option>' . "\n";
  }

  echo
  '                   </select></td>' . "\n" .
  '              </tr>' . "\n" .
  '              </table>' . "\n" .
  '            </div>' . "\n" .
  '          </div>' . "\n" .
  '        </div>' . "\n" .
  '        <div>' . "\n" .
  '          <div class="submit">' . "\n" .
  '            <div class="div-wait" id="divwait2"><img src="' . JBFPURL . 'img/loading.gif" /></div>' . "\n" .
  '            <input type="submit" class="button-secondary" value="Save Changes" id="jbf_save_btn" name="jbf_update_options_btn" onclick="document.getElementById(nameofDivWait).style.display=\'inline\';this.form.submit();" />' . "\n" .
  '            <div class="div-wait" id="divwait"><img src="' . JBFPURL . 'img/loading.gif" /></div>' . "\n" .
  '            <input type="submit" class="button-primary" value="Import From JonBensonFitness.com" id="jbf_import_posts_btn" name="jbf_import_btn" onclick="document.getElementById(nameofDivWait).style.display=\'inline\';this.form.submit();" />' . "\n" .    
  '          </div>' . "\n" .
  '        </div>' . "\n" .
  '      </div>' . "\n" .
  '    </div>' . "\n" .
  '  </div>' . "\n" .
  '  </form>' . "\n" .
  '</div' . "\n";
 
}




?>