<?php

class CSV_HELPER extends SOAH_BASE{

	function __construct(){

		add_action( 'wp_ajax_reset_locations', array( $this, 'reset_locations' ) );

	}

	function reset_locations(){
		$terms = get_terms( 'locations', array( 'fields' => 'ids', 'hide_empty' => false ) );
		foreach ( $terms as $value ) {
			wp_delete_term( $value, 'locations' );
		}
	}




}

CSV_HELPER::getInstance();
