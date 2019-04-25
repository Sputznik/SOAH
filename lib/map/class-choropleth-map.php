<?php

class CHOROPLETH_MAP extends SOAH_BASE{

  function __construct(){

		add_shortcode( 'soah_map', array( $this, 'shortcode' ) );

    /** TO LOAD THE ASSETS - SCRIPTS AND STYLES */
		add_action( 'the_posts', array( $this, 'assets' ) );

    add_action( 'wp_ajax_map_data', array( $this, 'map_data' ) );
    add_action( 'wp_ajax_nopriv_map_data', array( $this, 'map_data' ) );

	}

  function getReportCount( $terms = array(), $year = 0 ){

    $query_args = array(
      'post_type'       => 'reports',
      'posts_per_page'  =>  -1,
    );

    // ADD TERM PARAMS
    if( is_array( $terms ) && count( $terms ) ){
      $query_args['tax_query'] = array();
      foreach( $terms as $term ){
        $temp = array(
          'taxonomy' =>  $term['taxonomy'],
          'field'    =>  'name',
          'terms'    =>  $term['values']
        );
        array_push( $query_args['tax_query'], $temp );
      }
    }

    // ADD DATE PARAMS
    if( $year > 0 ){
      $query_args['date_query'] = array(
        'year'  => $year
      );
    }

    $query = new WP_Query( $query_args );

    return $query->found_posts;
  }

  function map_data(){

    $year = 0;
    if( isset( $_GET['cf_year'] ) ){
      $year = $_GET['cf_year'];
    }

    // GET STATE ID IF THE STATE NAME HAS BEEN PASSED
    $state_id = 0;
    if( isset( $_GET['tax_locations'] ) ){
      $stateTerm = get_term_by( 'name', $_GET['tax_locations'], 'locations' );
      if( isset( $stateTerm->term_id ) && $stateTerm->term_id ){
        $state_id = $stateTerm->term_id;
      }
    }

    // FINAL DATA
    $data = array();

    // QUERY THE LOCATIONS TABLE
    $term_query_args = array(
      'hide_empty'  => false,
      'taxonomy'    => 'locations',
      //'number'      => 5
    );
    if( $state_id ){
      $term_query_args['child_of'] = $state_id;
    }
    $terms = get_terms( $term_query_args );

    $extra_taxonomies = array('report-type', 'victims');

    // ITERATE THROUGH EACH TERM - LOCATIONS WHICH IS INCLUSIVE OF STATE AND DISTRICTS
    foreach( $terms as $term ){
      if( $term->parent ){

        $report_count_tax_args = array(
          array(
            'taxonomy'  => 'locations',
            'values'    => array( $term->name )
          )
        );

        // NARROW DOWN THE COUNT BASED ON EXTRA TERMS OF TAXONOMIES THAT MAY BE PASSED
        foreach( $extra_taxonomies as $taxonomy ){
          $passed_terms = array();
          if( isset( $_GET[ 'tax_'.$taxonomy ] ) ){
            $passed_terms = $_GET[ 'tax_'.$taxonomy ];
          }
          if( is_array( $passed_terms ) && count( $passed_terms ) ){
            array_push( $report_count_tax_args, array(
              'taxonomy'  => $taxonomy,
              'values'    => $passed_terms
            ) );
          }
        }

        $report_count = $this->getReportCount( $report_count_tax_args, $year );

        $temp = array(
          'district'  => $term->name,
          'reports'   => $report_count > 0 ? $report_count : rand( 0, 100 )
        );
        array_push( $data, $temp );
      }
    }


    //echo "<pre>";
    //print_r( $data );
    //echo "</pre>";

    print_r( wp_json_encode( $data ) );

    wp_die();
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
			'title'	=> 'A Simple Choropleth Map',
      'url'   => admin_url('admin-ajax.php?action=map_data')
		), $atts, 'soah_map' );

    $atts['color_rules'] = array(
      'default' => '#EDE7F6',
      'min'	=> array(
        'value'	=> 30,
        'color'	=> '#B39DDB'
      ),
      'max'	=> array(
        'value'	=> 85,
        'color'	=> '#311B92'
      ),
      'ranges'  => array(
        array(
          'min_value' => 60,
          'max_value'	=> 85,
          'color'			=> '#5E35B1'
        ),
        array(
          'min_value' => 30,
          'max_value'	=> 59,
          'color'			=> '#7E57C2'
        ),
      )
    );


		ob_start();

    include( "templates/map.php" );
		return ob_get_clean();
	}

}

CHOROPLETH_MAP::getInstance();
