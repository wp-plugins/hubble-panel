<?php

/*
Plugin Name: Hubble Panel
Description: Fetch images from HubbleSite NewsCenter and displays it on your blog.
Version: 1.0
Author: junatik
License: GPL2
*/

define(hp_TITLE, 'HubblePanel');
define(hp_HUBBLE_RSS, 'http://hubblesite.org/newscenter/newscenter_rss.php');
define(hp_IMG_COUNT, '9');
define(hp_PANEL_WIDTH, '300');
define(hp_DIR, basename(dirname(__FILE__)));
include_once(ABSPATH . WPINC . '/rss.php');

function hp_GetImage($args)  {
  $options = get_option('hp_widget');
  if($options == false)  {
    $options['hp_title'] = hp_TITLE;
    $options['hp_panel_width'] = hp_PANEL_WIDTH;
    $options['hp_img_count'] = hp_IMG_COUNT;
  }
  $rss = fetch_rss(hp_HUBBLE_RSS);
  $rss_count = count($rss->items);
  if($rss_count != 0)  {
    $style_id = time();
    $output  = '<style>#imgstyle'.$style_id.' a:hover img { filter:alpha(opacity=50); ..-opacity:0.5; opacity:0.7; -khtml-opacity:0.5; }</style>'; 
    $output .= '<div id="imgstyle'.$style_id.'" style="width:'.$options['hp_panel_width'].'px">';
    for($i=0; $i<$options['hp_img_count'] && $i<$rss_count; $i++)  {			
      $output .= '<a href="'.$rss->items[$i]['link'].'" target="_blank"><img src='.$rss->items[$i]['stsci']['thumbnailimageurl'].' border="0"></a>';
    }
    $output .= '</div>';
  }
  $title = $options['hp_title'];
  extract($args);	
  echo $before_widget;
  echo $before_title . $title . $after_title;
  echo $output;
  echo $after_widget;
}

function hp_widget_Admin()  {
  $options = $newoptions = get_option('hp_widget');	
  if($options == false)  {
    $newoptions['hp_title'] = hp_TITLE;
    $newoptions['hp_img_count'] = hp_IMG_COUNT;
    $newoptions['hp_panel_width'] = hp_PANEL_WIDTH;
  }
  if($_POST['hp_widget-submit'])  {
    $newoptions['hp_title'] = strip_tags(stripslashes($_POST['hp_title']));
    $newoptions['hp_img_count'] = $_POST['hp_img_count'];
    $newoptions['hp_panel_width'] = $_POST['hp_panel_width'];
  }	
  if($options != $newoptions)  {
    $options = $newoptions;		
    update_option('hp_widget', $options);
  }
  $hp_title = wp_specialchars($options['hp_title']);
  $hp_img_count = $options['hp_img_count'];
  $hp_panel_width = $options['hp_panel_width'];

?>
<form method="post" action="">	
<p><label for="hp_title"><?php _e('Title:'); ?> <input style="width: 180px;" id="hp_title" name="hp_title" type="text" value="<?php echo $hp_title; ?>" /></label></p>
<p><label for="hp_img_count"><?php _e('Images:'); ?> <input id="hp_img_count" name="hp_img_count" size="3" maxlength="3" type="text" value="<?php echo $hp_img_count?>" /></label></p>
<p><label for="hp_panel_width"><?php _e('Panel Width:'); ?> <input id="hp_panel_width" name="hp_panel_width" size="3" maxlength="3" type="text" value="<?php echo $hp_panel_width?>" /></label></p>
<br clear='all'></p>
<input type="hidden" id="hp_widget-submit" name="hp_widget-submit" value="1" />	
</form>
<?php
}
function hp_Init()  {
  register_sidebar_widget(__(hp_TITLE), 'hp_GetImage');
  register_widget_control(__(hp_TITLE), 'hp_widget_Admin', 250, 250);
}

add_action("plugins_loaded", "hp_Init");

?>
