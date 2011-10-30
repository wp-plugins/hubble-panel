<?php

/*
Plugin Name: Hubble Panel
Description: Fetch images from HubbleSite NewsCenter and displays it on your blog.
Version: 1.1
Author: pagepro.com.ua
License: GPL2
*/

define(hp_TITLE, 'Hubble Panel');
define(hp_HUBBLE_RSS, 'http://hubblesite.org/newscenter/newscenter_rss.php');
define(hp_IMG_COUNT, '9');
define(hp_PANEL_WIDTH, '300');
define(hp_DIR, basename(dirname(__FILE__)));
include_once(ABSPATH . WPINC . '/rss.php');


function widget_hpwidget_init() {
	if(!function_exists('register_sidebar_widget'))
		return;
		
function widget_hpwidget($args)  {
	extract($args);	
  	$options = get_option('widget_hpwidget');
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
    	$output .= '<div id="imgstyle'.$style_id.'" style="width:'.$options['hpwidget_width'].'px">';
    	for($i=0; $i<$options['hpwidget_count'] && $i<$rss_count; $i++)  {			
      		$output .= '<a href="'.$rss->items[$i]['link'].'" target="_blank"><img src='.$rss->items[$i]['stsci']['thumbnailimageurl'].' border="0"></a>';
    	}
    	$output .= '</div>';
  	}
	$title = $options['hpwidget_title'];
	echo $before_widget;
	echo $before_title . $title . $after_title;
	echo $output;
	echo $after_widget;
}
		
	function widget_hpwidget_control() {
		$options = get_option('widget_hpwidget');
  		if(!$options)  {
    		$options['hpwidget_title'] = hp_TITLE;
    		$options['hpwidget_count'] = hp_IMG_COUNT;
    		$options['hpwidget_width'] = hp_PANEL_WIDTH;    		
  		}
		if ($_POST['hpwidget-submit']) {
			$options['hpwidget_title'] = strip_tags(stripslashes($_POST['hp_title']));
			$options['hpwidget_count'] = strip_tags(stripslashes($_POST['hp_img_count']));
			$options['hpwidget_width'] = strip_tags(stripslashes($_POST['hp_panel_width']));			
			update_option('widget_hpwidget', $options);
		}
		
		$title = htmlspecialchars($options['hpwidget_title'], ENT_QUOTES);
		$items = htmlspecialchars($options['hpwidget_count'], ENT_QUOTES);
		$width = htmlspecialchars($options['hpwidget_width'], ENT_QUOTES);


echo '<p><label for="hp_title">'. _e('Title:').'<input style="width: 180px;" id="hp_title" name="hp_title" type="text" value="'.$title.'" /></label></p>';
echo '<p><label for="hp_img_count">'. _e('Images:').' <input id="hp_img_count" name="hp_img_count" size="3" maxlength="3" type="text" value="'.$items.'" /></label></p>';
echo '<p><label for="hp_panel_width">'._e('Panel Width:').' <input id="hp_panel_width" name="hp_panel_width" size="3" maxlength="3" type="text" value="'.$width.'" /> px</label></p>';
echo '<input type="hidden" id="hpwidget-submit" name="hpwidget-submit" value="1" />';
}
	register_widget_control('Hubble Panel', 'widget_hpwidget_control', 200, 200);		
	wp_register_sidebar_widget(sanitize_title('Hubble Panel'), 'Hubble Panel', 'widget_hpwidget', array('description' => __('Fetch images from HubbleSite NewsCenter and displays it on your blog.')));
}

add_action("plugins_loaded", "widget_hpwidget_init");

?>
