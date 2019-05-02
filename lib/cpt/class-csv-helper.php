<?php

class CSV_HELPER extends SOAH_BASE{



	function __construct(){

		/* SAMPLE ACTION HOOK FOR AJAX CALL
    add_action('orbit_batch_action_import_reports', function(){

			$offset = ( $_GET['orbit_batch_step'] - 1 ) * $_GET['per_page'];
			if( ! $offset ){ $offset = 1; }

			$path_posts_csv = get_stylesheet_directory().'/lib/cpt/csv/posts.csv';

			$arrayCsv = $this->toArray( $path_posts_csv );

			$selected_array_csv = array_slice( $arrayCsv, $offset, $_GET['per_page'] );

			$this->import_reports( $selected_array_csv );

			echo count( $selected_array_csv )." reports have been imported";

			/*
			echo "<pre>";
			print_r( $selected_array_csv );
			echo "</pre>";


		});
		*/

		add_action( 'wp_ajax_reset_locations', array( $this, 'reset_locations' ) );

		add_action( 'wp_ajax_reset_reports', array( $this, 'reset_reports' ) );

		add_action( 'wp_ajax_bulk_set_terms', array( $this, 'bulk_set_terms' ) );

	}

	function bulk_set_terms(){

		$count = 0;

		if( isset( $_GET['taxonomy1'] ) && ( isset( $_GET['taxonomy2'] ) ) && ( isset( $_GET['term1'] ) ) && isset( $_GET['term2'] ) ){
			$query = new WP_Query( array(
	    	'post_type' 		=> 'reports',
	      'posts_per_page'=>-1,
	      'tax_query' 		=> array(
	        array(
	          'taxonomy' => $_GET['taxonomy1'],
	          'field'    => 'slug',
	          'terms'    => $_GET['term1'],
	        ),
	     ) ) );

			while ($query->have_posts()) {
	    	$count++;
	      $query->the_post();
				global $post;
	      wp_set_object_terms( $post->ID , $_GET['term2'], $_GET['taxonomy2'] );
	    }
			wp_reset_query();
		}


		


    echo "<h1>$count posts have been updated</h1>";


		wp_die();
	}

	function reset_reports(){
		echo "Deleting reports <br>";
		$reports = get_posts( array('post_type'=>'reports','numberposts'=>-1) );
		print_r( $reports );

		foreach( $reports as $report ) {
			wp_delete_post( $report->ID, true );
		}

		wp_die();
	}

	function reset_locations(){
		$terms = get_terms( 'locations', array( 'fields' => 'ids', 'hide_empty' => false ) );
		foreach ( $terms as $value ) {
			wp_delete_term( $value, 'locations' );
		}
	}

	function toArray( $path ){
		$orbit_csv = ORBIT_CSV::getInstance();
		return $orbit_csv->toArray( $path );
	}

	/*
	* USED IN IMPORT_REPORTS function
	* TAKES THE TAXONOMY AS PARAMETER
	* RETURNS AN ARRAY OF ELEMENTS WITH SLUG AND ID OF TERMS
	*/
	function getTermsArr( $taxonomy ){
	  $terms = get_terms( $taxonomy, array('hide_empty' =>  false));
	  $data = array();
	  foreach ($terms as $term) {
	    $slug = $this->slugify( $term->name );
	    $data[ $slug ] = $term->term_id;
	  }
	  return $data;
	}

	function slugify( $label ){
		return preg_replace('/\s+/', '', strtoupper( $label ) );
	}
	/*
	function import_reports( $selected_array_csv ){

		$locations_arr = $this->getTermsArr('locations');

		//echo "<pre>";
		//print_r( $locations_arr );
		//echo "</pre>";

		foreach ( $selected_array_csv as $rowCsv ) {
			$post_id = 0;
			if( $rowCsv[1] ){

				$new_post = array(
	        'post_title'  =>  $rowCsv[1],
	        'post_content'=>  $rowCsv[4],
	        'post_date'   =>  $rowCsv[2],
	        'post_status' =>  'publish',
	        'post_type'   =>  'reports'
	      );
				//echo "<pre>";
				//print_r( $new_post );
				//echo "</pre>";

				$post_id = wp_insert_post($new_post);
			}


			if( $post_id ){
				$location_id_arr = array();
	      $locations = explode(',',$rowCsv[3]);
	      foreach( $locations as $location_str ){
	         $location_slug = $this->slugify( $location_str );
	         if( isset( $locations_arr[ $location_slug ] ) ){
	           array_push( $location_id_arr, $locations_arr[ $location_slug ] );
	        }
	      }
				wp_set_post_terms( $post_id, $location_id_arr, 'locations' );

				// Add report-type if not exists
	      $report_type_id_arr = array();
	      $report_types = explode( ',', $rowCsv[5] );
	      foreach( $report_types as $report_str ){
	        $term = term_exists( $report_str, 'report-type' );
	        if( !$term ){
	          $term = wp_insert_term( $report_str, 'report-type' );
	        }
	        array_push( $report_type_id_arr, $term['term_id'] );
	      }
				wp_set_post_terms( $post_id, $report_type_id_arr, 'report-type' );
			}




		}



	}
	*/



}

CSV_HELPER::getInstance();
