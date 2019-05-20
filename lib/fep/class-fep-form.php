<?php

class FEP_FORM extends SOAH_BASE{

	function __construct(){

		add_shortcode( 'soah_fep', array( $this, 'shortcode' ) );
	}

	function save(){

		if( $_POST ){
			echo "<pre>";print_r( $_POST );echo "</pre>"; wp_die();
			wp_verify_nonce( $_REQUEST['_wpnonce'], 'soah-fep' );

			// IF REQUIRED FIELDS ARE NOT PRESENT IN $_POST THE RETURN ERROR
		  $required_fields = array('incident-date', 'state', 'district');
			foreach( $required_fields as $field ){
		    if( !isset( $_POST[ $field ] ) || !$_POST[ $field ] ){ return 0; }
		  }

		 // echo "<pre>";
		 	//print_r( $_POST );
		  //echo "</pre>";

		  $post_id = $this->insertPost( array(
		  	'post_content'  => ( isset( $_POST['description'] ) && $_POST['description'] ) ? $_POST['description'] :  "Blank Incident Information",
		    'post_date'     => $_POST['incident-date']
		  ) );

			//print_r( $post_id );

			// IF POST ID IS NOT VALID THEN RETURN ERROR
		  if( !$post_id || is_array( $post_id ) ){ print_r( $post_id );return 0; }

			// SAVE TAXONOMIES
		  $taxonomies = array(
		  	'report-type' => $_POST['report-type'],
		    'victims'     => $_POST['victims'],
		    'locations'   => array()
		  );
		  if( isset( $_POST['state'] ) && isset( $_POST['district'] ) ){
		  	$taxonomies['locations'] = array( $_POST['state'], $_POST['district'] );
		  }
		  $this->setTermsToPost( $post_id, $taxonomies );

		 	// INSERTION OF CUSTOM FIELDS
		  if( isset( $_POST['links'] ) ){
		  	$_POST['incident-links'] = implode( "\r\n", $_POST['links'] );
		  }
		  $metafields = array( 'contact-name', 'contact-phone', 'contact-email', 'incident-address', 'incident-links' );
		  $this->updatePostMeta( $post_id, $metafields, $_POST );

		  // IMAGE UPLOAD
		  if( $_FILES ){ $this->handleMediaUpload( $post_id, $_FILES ); }


		}
		return 1;
	}

	// INSERT NEW POST TO THE DATABASE
	function insertPost( $data = array() ){

		$new_post = wp_parse_args( $data, array(
			'post_title'    => '',
			'post_content'	=> 'Blank Report',
			'post_status'   => 'pending',         // Choose: publish, preview, future, draft, etc.
      'post_type'     => 'reports'
		) );

		//print_r( $new_post );

		//save the new post and return its ID
    $post_id = wp_insert_post( $new_post, true );

		return $post_id;
	}

	/*
	* ADD TERMS TO THE POST
	* $taxonomies = array( 'locations' => array(1,2,3) )
	*/
	function setTermsToPost( $post_id, $taxonomies = array() ){
		foreach( $taxonomies as $taxonomy => $terms ){
      if( is_array( $terms ) && count( $terms ) ){
        wp_set_post_terms( $post_id, $terms, $taxonomy );
      }
    }
	}

	// SET CUSTOM FIELDS INFORMATION
	function updatePostMeta( $post_id, $metafields, $metadata ){
		foreach( $metafields as $metafield ){
			if( isset( $metadata[ $metafield ] ) ){
				update_post_meta( $post_id, $metafield, $metadata[ $metafield ] );
			}
		}
	}

	function handleMediaUpload( $post_id, $data = array() ){
		if( is_array( $data ) ){
      require_once( ABSPATH . 'wp-admin/includes/image.php' );
      require_once( ABSPATH . 'wp-admin/includes/file.php' );
      require_once( ABSPATH . 'wp-admin/includes/media.php' );

      foreach( $data as $key => $value ){
        $movefile = media_handle_upload( $key, $post_id, array( 'test_form'=> false ) );
      }
    }
	}

	// GET TRANSLATED VALUE FROM THE TRANSLATION FRAMEWORK BASED ON A SLUG
	function getTranslatedValue( $key, $lang, $slug, $default = '' ){

		$value = "";
		if( class_exists('ORBIT_TRANSLATIONS') ){
			$orbit_translation = ORBIT_TRANSLATIONS::getInstance();
			$value = $orbit_translation->getValue( $key, $lang, $slug );
		}

		if( !$value ){
			return $default;
		}

		return $value;
	}

	// RETURNS A LIST OF OPTION ARRAY FORMED FROM TERMS OF A TAXONOMY - USED FOR DROPDOWN OR CHECKBOXES
	function getOptionsFromTaxonomy( $taxonomy, $lang, $args = array( 'hide_empty' => false, 'orderby' => 'term_id' ) ){

		$args['taxonomy'] = $taxonomy;

		$options = array();
		$terms  =   get_terms( $args );
		foreach( $terms as $term ){
			$temp = array(
				'slug'  => $term->term_id,
				'title' => $term->name,
				'parent'	=> $term->parent
			);

			if( $lang != 'en' ){
				$temp['title']	= $this->getTranslatedValue( $taxonomy, $lang, $term->term_id, $term->name );
			}
			else{
				// ONLY FOR ENGLISH TRANSLATION
				if( isset( $term->description ) && $term->description ){
					$temp['title'] = $temp['title']." <small>(".$term->description.")</small>";
				}
			}

			array_push( $options, $temp );
		}
		return $options;
	}

	function display_inline_section( $section ){
		$section['class'] = isset( $section['class'] ) ? $section['class'] : "";
		$section['class'] .= " inline-section";

		echo "<div class='" . $section['class'] . "'>";
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
		echo "</div></div>";
	}

	function display_field( $field ){


		switch( $field['type'] ){
			case 'nested-fields':
				$this->display_inline_section( $field );
				break;
			default:
				$field['class'] = isset( $field['class'] ) ? $field['class']." form-field" : "form-field";
				echo "<div class='".$field['class']."'>";
				echo "<label>".$field['label']."</label>";
				include( "templates/" . $field['type'] . ".php" );
				echo "</div>";
		}


	}
	function display_section( $section, $args ){

		$args = wp_parse_args( $args, array(
			'prev_text'		=> "Previous",
			'next_text'		=> "Next",
			'submit_text'	=> "Submit",
			'totalSlides'	=> 1,
			'i'						=> 0

		) );

		echo "<section class='meteor-slide'>";
		$this->display_inline_section( $section );


		_e( "<ul class='meteor-list meteor-list-inline'>" );

		// HIDE IN THE FIRST PAGE OF THE FORM
		if( $args['i'] ){ _e( "<li><button data-behaviour='meteor-slide-prev'>" . $args['prev_text'] . "</button></li>" ); }

		// IN THE LAST FORM, THE TEXT SHOULD CHANGE TO SUBMIT
		if( $args['i'] != $args['totalSlides'] - 1 ){
			_e( "<li><button data-behaviour='meteor-slide-next'>" . $args['next_text'] . "</button></li>" );
		}
		else{
			_e( "<li><button type='submit'>" . $args['submit_text'] ."</button></li>" );
		}

		_e( "</ul>" );

		echo "</section>";
	}

	function shortcode( $atts ){

		$atts = shortcode_atts( array(
			'lang'				=> 'en',
			'redirect_to'	=> ''
		), $atts, 'soah_fep' );

		if( isset( $_GET['lang'] ) ){
			$atts['lang'] = $_GET['lang'];
		}

		ob_start();
		include( 'templates/form.php' );
		return ob_get_clean();
	}

	function getLabels(){
		$labels = apply_filters( 'soah-fep-labels', array() );
		return $labels;
	}

}


FEP_FORM::getInstance();
