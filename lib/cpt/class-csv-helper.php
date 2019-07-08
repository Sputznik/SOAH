<?php

class CSV_HELPER extends SOAH_BASE{

	function __construct(){

		add_shortcode( 'soah_export', array( $this, 'export_shortcode' ) );

		//add_action( 'wp_ajax_reset_locations', array( $this, 'reset_locations' ) );

		//add_action( 'wp_ajax_reset_reports', array( $this, 'reset_reports' ) );

		add_action( 'wp_ajax_bulk_set_terms', array( $this, 'bulk_set_terms' ) );

		/* ACTION HOOK FOR AJAX CALL - import terms */
    add_action('orbit_batch_action_soah_export', function(){

			// GET PARAMETERS
			$step = $_REQUEST['orbit_batch_step'];
			$file_slug = $_REQUEST['file_slug'];

			$header = array(
				'post_id',
				'post_title',
				'post_content',
				'post_date',
				'tax_locations',
				'tax_report-type[]',
				'tax_victims[]',
				'tax_meta-info[]'
			);

			$orbit_csv = ORBIT_CSV::getInstance();

			$header = $orbit_csv->prepareHeaderForExport( $header );

			$headerInfo = $orbit_csv->getHeaderInfo( array( $header ) );

			// ADD HEADER ROW FOR THE FIRST BATCH REQUEST ONLY
			if( $step == 1 ){
				$orbit_csv->addHeaderToCSV( $file_slug, $header );
			}

			// GET QUERY ARGS
			$query_args = $this->queryArgs( $_REQUEST, $_REQUEST['posts_per_page'], $_REQUEST['orbit_batch_step'] );

			$orbit_csv->exportPosts( $file_slug, $headerInfo, $query_args );

		});


	}

	// WRAPPER FUNCTION TO WPDB QUERY FOR REPORTS
	function queryArgs( $params, $posts_per_page, $paged = 1 ){

		$orbit_util = ORBIT_UTIL::getInstance();

		$query_args = array(
			'posts_per_page' => $posts_per_page,
			'post_type'			 => 'reports',
			'post_status'		 => 'publish',
			'paged'					 => $paged
		);

		if( isset( $params['tax'] ) && ( !empty( $params['tax'] ) ) ){
			$query_args['tax_query'] = $orbit_util->getTaxQueryParams( $params['tax'] );
		}

		if( isset( $params['date'] ) && ( !empty( $params['date'] ) ) ){
			$query_args['date_query'] = $orbit_util->getDateQueryParams( $params['date'] );
		}

		return $query_args;

	}

	// WRAPPER FUNCTION FOR OBRIT BATCH PROCESS
	function batchProcess( $atts ){
		$batch_process = ORBIT_BATCH_PROCESS::getInstance();
		echo $batch_process->plain_shortcode( $atts );
	}

	// RETURNS THE ABSOLUTE PATH AND URL OF A FILE IN THE WP CONTENTS DIRECTORY
	function getFilePath( $file_slug ){
		$orbit_csv = ORBIT_CSV::getInstance();
    return $orbit_csv->getFilePath( $file_slug );
	}

	// CONVERT THE GET OR PAST PARAMS INTO STRING FORMAT THAT CAN BE RECONVERTED LATER
	function paramsToString( $params ){
		$orbit_util = ORBIT_UTIL::getInstance();
		return $orbit_util->paramsToString( $params );
	}

	function export_shortcode( $atts ){
		$atts = shortcode_atts( array(), $atts, 'soah_export' );

		ob_start();
		include( 'templates/export.php' );
		return ob_get_clean();
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




}

CSV_HELPER::getInstance();
