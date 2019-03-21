<?php

class FEP_FORM extends SOAH_BASE{
	
	function __construct(){
		add_shortcode( 'soah_fep', array( $this, 'shortcode' ) );
	}
	
	function getOptionsFromTaxonomy( $taxonomy, $args = array( 'hide_empty' => false ) ){
		$options = array();
		$terms  =   get_terms( $taxonomy, $args );
		foreach( $terms as $term ){
			array_push( $options, array(
				'slug'  => $term->term_id,
				'title' => $term->name
			) );
		}
		return $options;
	}
	function display_field( $field ){
		$field['class'] = isset( $field['class'] ) ? $field['class']." form-field" : "form-field";
		echo "<div class='".$field['class']."'>";
		echo "<label>".$field['label']."</label>";
		include( "templates/" . $field['type'] . ".php" );
		echo "</div>";
	}
	function display_section( $section ){
		echo "<section class='".$section['class']."'>";
		if( isset( $section['title'] ) ){
			_e( "<h3>".$section['title']."</h3>" );
		}
		if( isset( $section['desc'] ) ){
			_e( "<p class='section-desc'>".$section['desc']."</p>" );
		}
		echo "<div class='section-fields'>";
		foreach( $section['fields'] as $field ){
			$this->display_field( $field );
		}
		echo "</div>";
		echo "</section>";
	}
	
	function shortcode( $atts ){
		ob_start();
		include( 'templates/form.php' );
		return ob_get_clean();
	}
	
}


FEP_FORM::getInstance();