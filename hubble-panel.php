<?php

/**
Plugin Name: Hubble Panel
Description: Displays amazing images and latest information from Hubble Space Telescope on your blog.
Version: 1.5
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
			
		'casthd'		=>	array(
			'name'		=>	'Hubblecast HD',
			'feed'		=>	'http://feeds.feedburner.com/hubblecast?format=xml'),
	);
		
	
	function __construct()	{
		$options = array(
            'description'   =>  'Displays amazing images and latest information from Hubble Space Telescope on your blog.',
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
		'feed'				=> 'nebulae'
		);

		$instance = wp_parse_args((array)$instance, $defaults);
		$title = ! empty($instance['title']) ? $instance['title'] : '';
		$img_per_page = ! empty($instance['img_per_page']) ? $instance['img_per_page'] : '';
		$desc = ! empty($instance['show_descriptions']) ? $instance['show_descriptions'] : '';
		$trim = ! empty($instance['trim_description']) ? $instance['trim_description'] : '';
		$feed = ! empty($instance['feed']) ? $instance['feed'] : '';
		?>
		<p>
			<label for="<?=$this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
			<input class="widefat" id="<?=$this->get_field_id('title'); ?>" name="<?=$this->get_field_name('title'); ?>" type="text" value="<?=esc_attr($title); ?>">
		</p>
		<p>
			<label for="<?=$this->get_field_id('feed'); ?>">Category</label>
			<select name="<?=$this->get_field_name('feed'); ?>">
			<?php foreach($this->feeds as $k=>$v) : ?>
				<option <?php selected($k, $feed); ?> value="<?=$k;?>"><?=$v['name'];?></option>
			<?php endforeach; ?>
			</select>
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
		
		echo $args['before_widget'];
		
		if($title)	{
			echo '<h3>'.$title.'</h3>';
		}

		$rss = fetch_feed($this->feeds[$feed]['feed']);
		
		switch($view_type)	{
				
			// Collage			
			case 'collage':				

			$maxitems = $rss->get_item_quantity($perpage); 
			$rss_items = $rss->get_items(0, $maxitems);
			foreach($rss_items as $item)	{
				$enc = $item->get_enclosures();?>
				<a href="<?=$item->get_permalink();?>" target="_blank" title="<?=$item->get_title();?>"><img style="width:60px; height:60px; padding:1px" src="<?=$enc[0]->link;?>" alt="<?=$item->get_title();?>" />
				<?php
			}		
			break;
			
			// List	
			case 'list':
			
			$maxitems = $rss->get_item_quantity($perpage); 
			$rss_items = $rss->get_items(0, $maxitems);
			foreach($rss_items as $item)	{
				//print_r($item);
				$enc = $item->get_enclosures();?>
				<a href="<?=$item->get_permalink();?>" target="_blank" title="<?=$item->get_title();?>"><img class="hubble-image" src="<?=$enc[0]->link;?>" alt="<?=$item->get_title();?>" />
				<h4><?=$item->get_title();?></h4>
				<?php if($desc):?>
				<div class="hp-description"><?=wp_trim_words($item->get_description(), $trim, $more = null);?></div>
				<?php endif; ?>
				</a><br/>
			<?php
			}		
			break;
			
		}
		
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

		return $instance;
	}

}

?>
