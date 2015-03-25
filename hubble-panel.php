<?php

/**
Plugin Name: Hubble Panel
Description: Displays images and latest information from Hubble Space Telescope on your blog.
Version: 1.6
Author: Limeira Studio
Author URI: http://www.limeirastudio.com/
License: GPL2
Copyright: Limeira Studio
*/

function register_hp_widget()	{
	register_widget('Hubble_Panel');
}
add_action('widgets_init', 'register_hp_widget');

class Hubble_Panel extends WP_Widget {
	
	var $feeds = array(
		'newscenter'	=> 	array(
			'name'		=>	'NewsCenter',
			'feed'		=>	'http://hubblesite.org/newscenter/newscenter_rss.php'),
		
		'solarsystem'	=>	array(
			'name'		=>	'Solar System',
			'feed'		=>	'http://www.spacetelescope.org/images/feed/category/solarsystem/'),
		
		'exoplanets'	=>	array(
			'name'		=>	'Exoplanets',
			'feed'		=>	'http://www.spacetelescope.org/images/feed/category/exoplanets/'),
			
		'stars'			=>	array(
			'name'		=>	'Stars',
			'feed'		=>	'http://www.spacetelescope.org/images/feed/category/star/'),
			
		'clusters'		=>	array(
			'name'		=>	'Star Clusters',
			'feed'		=>	'http://www.spacetelescope.org/images/feed/category/starcluster/'),
			
		'nebulae'		=>	array(
			'name'		=>	'Nebulae',
			'feed'		=>	'http://www.spacetelescope.org/images/feed/category/nebulae/'),
			
		'galaxies'		=>	array(
			'name'		=>	'Galaxies',
			'feed'		=>	'http://www.spacetelescope.org/images/feed/category/galax/'),
			
		'blackholes'	=>	array(
			'name'		=>	'Black Holes & Quasars',
			'feed'		=>	'http://www.spacetelescope.org/images/feed/category/quasar/'),
			
		'cosmology'		=>	array(
			'name'		=>	'Cosmology',
			'feed'		=>	'http://www.spacetelescope.org/images/feed/category/cosmo/'),
			
		'jwst'			=>	array(
			'name'		=>	'James Webb Space Telescope',
			'feed'		=>	'http://www.spacetelescope.org/images/feed/category/jwst/'),
			
		'miscellaneous'	=>	array(
			'name'		=>	'Miscellaneous',
			'feed'		=>	'http://www.spacetelescope.org/images/feed/category/misc/'),
			
		'illustrations'	=>	array(
			'name'		=>	'Illustrations',
			'feed'		=>	'http://www.spacetelescope.org/images/feed/category/illustration/'),
			
		'spacecraft'	=>	array(
			'name'		=>	'Spacecraft',
			'feed'		=>	'http://www.spacetelescope.org/images/feed/category/spacecraft/'),
			
		'mission'		=>	array(
			'name'		=>	'Launch/Servicing Missions',
			'feed'		=>	'http://www.spacetelescope.org/images/feed/category/mission/'),
			
		'picofweek'		=>	array(
			'name'		=>	'Picture of The Week',
			'feed'		=>	'http://feeds.feedburner.com/hubble_potw?format=xml'),
			
	);
		
	
	function __construct()	{
		$options = array(
            'description'   =>  'Displays images and latest information from Hubble Space Telescope on your blog.',
            'name'          =>  'Hubble Panel'
        );
		parent::__construct('hubble_panel', '', $options);
	}
	
	public function form($instance)	{
		$defaults = array(
		'title'				=> 'Hubble Panel',
		'img_per_page'		=> '9',
		'view_type'			=> 2,
		'trim_description'	=> 30,
		'feed'				=> 'nebulae',
		'lightbox'			=> 'on',
		'show_cat_title'	=> 'on'
		);

		$instance = wp_parse_args((array)$instance, $defaults);
		$title = ! empty($instance['title']) ? $instance['title'] : '';
		$img_per_page = ! empty($instance['img_per_page']) ? $instance['img_per_page'] : '';
		$desc = ! empty($instance['show_descriptions']) ? $instance['show_descriptions'] : '';
		$trim = ! empty($instance['trim_description']) ? $instance['trim_description'] : '';
		$feed = ! empty($instance['feed']) ? $instance['feed'] : '';
		$random_cats = ! empty($instance['random_cats']) ? $instance['random_cats'] : '';
		$lightbox = ! empty($instance['lightbox']) ? $instance['lightbox'] : '';
		$show_cat_title = ! empty($instance['show_cat_title']) ? $instance['show_cat_title'] : '';
		?>

		<p>
			<label for="<?=$this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
			<input class="widefat" id="<?=$this->get_field_id('title'); ?>" name="<?=$this->get_field_name('title'); ?>" type="text" value="<?=esc_attr($title); ?>">
		</p>
		<p>
			<label for="<?=$this->get_field_id('feed'); ?>">Category</label>
			<select name="<?=$this->get_field_name('feed'); ?>" id="<?=$this->get_field_id('feed'); ?>">
			<?php foreach($this->feeds as $k=>$v) : ?>
				<option <?php selected($k, $feed); ?> value="<?=$k;?>"><?=$v['name'];?></option>
			<?php endforeach; ?>
			</select><br/>
			<input class="checkbox" type="checkbox" <?php checked($random_cats, 'on'); ?> id="<?=$this->get_field_id('random_cats'); ?>" name="<?=$this->get_field_name('random_cats'); ?>" /> 
		    <label for="<?=$this->get_field_id('random_cats'); ?>"> Random</label>
		    
		    <input class="checkbox" type="checkbox" <?php checked($show_cat_title, 'on'); ?> id="<?=$this->get_field_id('show_cat_title'); ?>" name="<?=$this->get_field_name('show_cat_title'); ?>" /> 
		    <label for="<?=$this->get_field_id('show_cat_title'); ?>"> Show Title</label>
		    
		</p>
		<h4>View:</h4>	
		<p>
		<?php
		$view_type = (isset($instance['view_type']) && is_numeric($instance['view_type'])) ? (int) $instance['view_type'] : 0;
	    for($n = 1; $n < 3; $n++)	{
	    	echo ($n == 1) ? 'List ' : 'Collage ';
	    	echo '<input type="radio" id="'.$this->get_field_id('view_type').'-'.$n.'" name="'.$this->get_field_name('view_type').'" value="'.$n.'" '. checked($view_type == $n, true, false) .'>';
	    }
		?>
		</p>
		<p>
		    <input class="checkbox" type="checkbox" <?php checked($lightbox, 'on'); ?> id="<?=$this->get_field_id('lightbox'); ?>" name="<?=$this->get_field_name('lightbox'); ?>" /> 
		    <label for="<?=$this->get_field_id('lightbox'); ?>"> Enable Lightbox Effect</label>
		</p>
		<p>
			<label for="<?=$this->get_field_id('img_per_page'); ?>">
			<input id="<?=$this->get_field_id('img_per_page'); ?>" name="<?=$this->get_field_name('img_per_page'); ?>" size="3" maxlength="3" type="text" value="<?=esc_attr($img_per_page); ?>" /> Items</label>		
		</p>
		<h4>Descriptions (List View)</h4>
		<p>
		    <input class="checkbox" type="checkbox" <?php checked($desc, 'on'); ?> id="<?=$this->get_field_id('show_descriptions'); ?>" name="<?=$this->get_field_name('show_descriptions'); ?>" /> 
		    <label for="<?=$this->get_field_id('show_descriptions'); ?>"> Show</label>
		</p>
		<p>    
			<label for="<?=$this->get_field_id('trim_description'); ?>">
			<input id="<?=$this->get_field_id('trim_description'); ?>" name="<?=$this->get_field_name('trim_description'); ?>" size="3" maxlength="3" type="text" value="<?=esc_attr($trim); ?>" /> Trim (Words)</label>		
		</p>
			<?php 
		}
	
	public function widget($args, $instance)	{
		$title = $instance['title'];
		$perpage = $instance['img_per_page'];
		$desc = $instance['show_descriptions'];
		$view_type = ($instance['view_type'] == 1) ? 'list' : 'collage';
		$trim = $instance['trim_description'];
		$feed = $instance['feed'];
		$random_cats = $instance['random_cats'];
		$show_cat_title = $instance['show_cat_title'];
		$lightbox = $instance['lightbox'];
		
		if($lightbox)	{
			$this->hp_register_lightbox();
		}
		
		echo $args['before_widget'];?>
		<style>
		.hubble-panel img	{
		-moz-transition:-moz-transform 0.5s ease-in; 
		-webkit-transition:-webkit-transform 0.5s ease-in; 
		-o-transition:-o-transform 0.5s ease-in;
		}
		.hubble-panel img:hover	{
		-moz-transform:scale(1.2); 
		-webkit-transform:scale(1.2);
		-o-transform:scale(1.2);
		}
		</style>
		<div class="hubble-panel">
		<?php
		if($title)	{
			echo '<h3 class="hubble-panel-widget-title">'.$title.'</h3>';
		}
		
		if($random_cats)	{
			$a = @reset($this->shuffle_assoc($this->feeds));
			$rss = fetch_feed($a['feed']);
		}	else {
			$rss = fetch_feed($this->feeds[$feed]['feed']);
		}
		if($show_cat_title)	{
			$title = ($random_cats) ? $a['name'] : $this->feeds[$feed]['name'];
			echo '<h5 class="hubble-panel-cat-title">'.$title.'</h5>';
		}
		
		switch($view_type)	{
				
			// Collage			
			case 'collage':				

			$maxitems = $rss->get_item_quantity($perpage); 
			$rss_items = $rss->get_items(0, $maxitems);
			$i=1;
			foreach($rss_items as $item)	{
				$enc = $item->get_enclosures();?>
				<a href="<?=($lightbox) ? $enc[0]->link : $item->get_permalink();?>" data-lightbox="roadtrip" data-title="<?=$item->get_title();?>" target="_blank" title="<?=$item->get_title();?>"><img style="width:70px; height:70px; padding:1px" src="<?=$enc[0]->link;?>" alt="<?=$item->get_title();?>" /></a>
				<?php
			$i++;
			}		
			break;
			
			// List	
			case 'list':
			
			$maxitems = $rss->get_item_quantity($perpage); 
			$rss_items = $rss->get_items(0, $maxitems);
			foreach($rss_items as $item)	{
				$enc = $item->get_enclosures();?>
				<a href="<?=($lightbox) ? $enc[0]->link : $item->get_permalink();?>" data-lightbox="roadtrip" data-title="<?=$item->get_title();?>" target="_blank" title="<?=$item->get_title();?>"><img class="hubble-panel-item-image" src="<?=$enc[0]->link;?>" alt="<?=$item->get_title();?>" /></a>
				<h4 class="hubble-panel-item-title"><a href="<?=$item->get_permalink();?>"><?=$item->get_title();?></a></h4>
				<?php if($desc):?>
				<div class="hubble-panel-item-description"><?=wp_trim_words($item->get_description(), $trim, $more = null);?></div>
				<?php endif; ?><br/>
			<?php
			}		
			break;
			
		}
		echo '</div>';
		echo $args['after_widget'];
	}

	public function update($new_instance, $old_instance)	{
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		$instance['img_per_page'] = (!empty($new_instance['img_per_page'])) ? strip_tags($new_instance['img_per_page']) : '';
		$instance['show_descriptions'] = (!empty($new_instance['show_descriptions'])) ? strip_tags($new_instance['show_descriptions']) : '';
		$instance['view_type'] = (isset($new_instance['view_type'])) ? strip_tags($new_instance['view_type']) : '';
		$instance['trim_description'] = (isset($new_instance['trim_description'])) ? strip_tags($new_instance['trim_description']) : '';
		$instance['feed'] = (isset($new_instance['feed'])) ? strip_tags($new_instance['feed']) : '';
		$instance['random_cats'] = (isset($new_instance['random_cats'])) ? strip_tags($new_instance['random_cats']) : '';
		$instance['lightbox'] = (isset($new_instance['lightbox'])) ? strip_tags($new_instance['lightbox']) : '';
		$instance['show_cat_title'] = (isset($new_instance['show_cat_title'])) ? strip_tags($new_instance['show_cat_title']) : '';
		
		return $instance;
	}
	
	private function shuffle_assoc($list)	{
		if(!is_array($list)) return $list;

		$keys = array_keys($list);
		shuffle($keys);
		$random = array();
		foreach($keys as $key)
			$random[$key] = $list[$key];

		return $random;
	}
	
	private function hp_register_lightbox()	{
		if(!is_admin()) {
			wp_enqueue_script('jquery', plugin_dir_url( __FILE__ ) . 'assets/lightbox/js/jquery-1.11.0.min.js', array(), '1.6', false);
			wp_enqueue_script('lightbox', plugin_dir_url( __FILE__ ) . 'assets/lightbox/js/lightbox.min.js', array(), '1.6', false);
			wp_enqueue_style('lightboxcss', plugin_dir_url( __FILE__ ) . 'assets/lightbox/css/lightbox.css', array(), '1.6', false);
		}		
	}	

}

?>
