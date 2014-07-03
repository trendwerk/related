<?php
/**
 * Plugin Name: Related
 * Description: Related posts. Solely based on taxonomies.
 */

class TP_Related extends WP_Query {
	var $args = array();
	
	/**
	 * Constructor
	 *
	 * @param int $post_id
	 *
	 * @return object WP_Query
	 */
	function __construct($post_id,$args=array()) {
		$this->post = get_post($post_id);
		
		$defaults = array(
			'post_type' => $this->post->post_type,
			'posts_per_page' => 5,
			'post__not_in' => array($post_id)
		);
		$this->args = wp_parse_args($args,$defaults);
		
		$related = $this->get_related($this->post->ID);
		if($related) :
			parent::__construct(wp_parse_args(array(
				'post__in' => $related,
				'orderby' => 'post__in'
			),$this->args));
		else :
			parent::__construct(array());
		endif;
	}
	
	/**
	 * Get related post ids
	 *
	 * @param int $post_id
	 *
	 * @return array Post ids
	 */
	function get_related($post_id) {
		$related = array();
		
		$taxonomies = get_object_taxonomies($this->post->post_type);
		if($taxonomies) :
			foreach($taxonomies as $taxonomy) :
				$terms = wp_get_post_terms($this->post->ID,$taxonomy);
				if($terms) :
					foreach($terms as $term) :
						$posts = new WP_Query(wp_parse_args(array(
							'posts_per_page' => '-1',
							'tax_query' => array(
								array(
									'taxonomy' => $taxonomy,
									'terms' => $term->slug,
									'field' => 'slug',
								),
							),
						),$this->args));
						
						if($posts->have_posts()) :
							while($posts->have_posts()) : 
								$posts->the_post();

								if( ! isset( $related[ get_the_ID() ] ) )
									$related[ get_the_ID() ] = 0;

								$related[ get_the_ID() ] += 1;
							endwhile;
						endif;
						wp_reset_postdata();
					endforeach;
				endif;
			endforeach;
		endif;
		
		//Put them in the right order and chop off what we don't need
		asort($related);
		$related = array_reverse($related,true);
		
		if($this->args['posts_per_page'] != -1) :
			$related = array_chunk($related,$this->args['posts_per_page'],true);
		else :
			$related = array($related);
		endif;

		return isset($related[0]) ? array_keys($related[0]) : array();
	}
}
