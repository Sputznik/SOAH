<?php

class CHOROPLETH_MAP extends SOAH_BASE{

  function __construct(){

		add_shortcode( 'soah_map', array( $this, 'shortcode' ) );

    /** TO LOAD THE ASSETS - SCRIPTS AND STYLES */
		add_action('the_posts', array( $this, 'assets') );
	}

  /** CHECK IF THE CONTENT HAS THE SHORTCODE */
	function has_shortcode( $content, $tag ) {
		if(stripos($content, '['.$tag.']') !== false)
			return true;
		return false;
	}

  /** LOAD SCRIPTS AND STYLES IF THE SHORTCODE IS USED */
	function assets($posts){

		$found = false;
		if ( !empty($posts) ){
			foreach ($posts as $post) {
				if ( $this->has_shortcode($post->post_content, 'soah_map') ){
					$found = true;
					break;
				}
			}
		}

		if( $found ){
      // STYLES FOR LEAFLET
      wp_enqueue_style( 'soah-leaflet', get_stylesheet_directory_uri() .'/assets/css/leaflet.1.4.0.css', array(), SOAH_VERSION );

      // STYLES FOR CHOROPLETH MAP
      wp_enqueue_style( 'soah-choropleth', get_stylesheet_directory_uri() .'/assets/css/choropleth.map.css', array(), SOAH_VERSION );

      // POPPER SCRIPT
      wp_enqueue_script( 'popper', get_stylesheet_directory_uri().'/assets/js/popper.1.14.3.js', array( 'jquery' ), SOAH_VERSION, true );

      // LEAFLET SCRIPT
      wp_enqueue_script( 'leaflet', get_stylesheet_directory_uri().'/assets/js/leaflet.1.4.0.js', array( 'jquery', 'popper' ), SOAH_VERSION, true );

      // LEAFLET GEOCSV SCRIPT
      wp_enqueue_script( 'leaflet-geocsv', get_stylesheet_directory_uri().'/assets/js/leaflet.geocsv.js', array( 'leaflet' ), SOAH_VERSION, true );

      // INDIA DISTRICTS SCRIPT
      wp_enqueue_script( 'leaflet-india-dist', get_stylesheet_directory_uri().'/assets/js/india_dist_662.js', array( 'leaflet' ), SOAH_VERSION, true );

      // INDIA STATES SCRIPT
      wp_enqueue_script( 'leaflet-india-states', get_stylesheet_directory_uri().'/assets/js/states.js', array( 'leaflet' ), SOAH_VERSION, true );

      // SAMPLE DATA SCRIPT
      wp_enqueue_script( 'soah-sample-data', get_stylesheet_directory_uri().'/assets/js/sample_data.js', array( 'leaflet' ), SOAH_VERSION, true );

      // CHOROPLETH MAP SCRIPT
      wp_enqueue_script( 'choropleth', get_stylesheet_directory_uri().'/assets/js/choropleth.js', array( 'leaflet' ), SOAH_VERSION, true );

		}

		return $posts;

	}


  function shortcode( $atts ){

		$atts = shortcode_atts( array(
			'title'	=> 'A Simple Choropleth Map'
		), $atts, 'soah_map' );

		ob_start();
		_e( "<div data-atts='".wp_json_encode( $atts )."' style='margin-top:80px;' data-behaviour='choropleth-map'></div>" );
		return ob_get_clean();
	}

}

CHOROPLETH_MAP::getInstance();
