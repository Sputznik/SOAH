<?php

$contact_form = new FEP_FORM;

define( 'SITE_KEY','6LflSJkUAAAAAO3sN5cTk_-a5d_0O1v3qzQRDN3W' );

$required_fields = array('incident-date', 'state', 'district');

$form_success_flag = 1;

if( isset( $_POST['submit'] ) ){

  foreach( $required_fields as $field ){
    if( !isset( $_POST[ $field ] ) || !$_POST[ $field ] ){
      $form_success_flag = 0;
    }
  }

  if( $form_success_flag ){

    //echo "<pre>";
    //print_r( $_POST );
    //echo "</pre>";


    // Add the content of the form to $post as an array
    $new_post = array(
      'post_title'    => isset( $_POST['title'] ) ? $_POST['title'] :  "",
      'post_content'  => isset( $_POST['description'] ) ? $_POST['description'] :  "",
      'post_date'     => $_POST['incident-date'],
      'post_status'   => 'pending',           // Choose: publish, preview, future, draft, etc.
      'post_type'     => 'reports'            // Use a custom post type if you want to
    );

    //save the new post and return its ID
    $post_id = wp_insert_post($new_post);

    //echo $post_id;

    // SAVE TAXONOMIES
    $taxonomies = array( 'report-type', 'victims', 'locations' );
    if( isset( $_POST['state'] ) && isset( $_POST['district'] ) ){
      $_POST['locations'] = array( $_POST['state'], $_POST['district'] );
    }
    foreach( $taxonomies as $taxonomy ){
      if( is_array( $_POST[ $taxonomy ] ) ){
        wp_set_post_terms( $post_id, $_POST[ $taxonomy ], $taxonomy );
      }
    }

    // INSERTION OF CUSTOM FIELDS
    if( isset( $_POST['links'] ) ){
      $_POST['incident-links'] = implode( "\r\n", $_POST['links'] );
    }
    $metafields = array( 'contact-name', 'contact-phone', 'contact-email', 'incident-address', 'incident-links' );
    foreach( $metafields as $metafield ){
      if( isset( $_POST[ $metafield ] ) ){
        update_post_meta( $post_id, $metafield, $_POST[ $metafield ] );
      }
    }

    // IMAGE UPLOAD
    if( $_FILES && is_array( $_FILES ) ){
      require_once( ABSPATH . 'wp-admin/includes/image.php' );
      require_once( ABSPATH . 'wp-admin/includes/file.php' );
      require_once( ABSPATH . 'wp-admin/includes/media.php' );

      foreach($_FILES as $key => $value){
        $movefile = media_handle_upload( $key, $post_id, array( 'test_form'=> false ) );
      }
    }
  }
}

$lang = "hi";

/* GETTING STATES AND DISTRICTS FROM THE DB */
$locations  =   get_terms( 'locations', array( 'hide_empty' => false ) );
$states = array();
$districts = array();
foreach( $locations as $location ){

  $temp = array(
    'slug' => $location->term_id,
    'title' => $location->name,
    'parent'  =>  $location->parent
  );

  if( $lang != 'en' ){
    $temp['title']	= $this->getTranslatedValue( 'locations', $lang, $location->term_id );
  }


  if( $location->parent == 0 ){
    array_push( $states, $temp );
  }
  else{
    array_push( $districts, $temp );
  }
}
/* GETTING STATES AND DISTRICTS FROM THE DB */

/* REPORT TYPES FROM THE DB */
$report_types = $contact_form->getOptionsFromTaxonomy( 'report-type', $lang );
$victims = $contact_form->getOptionsFromTaxonomy( 'victims', $lang );
/* REPORT TYPES FROM THE DB */



$labels = $this->getLabels();



$form_sections = array(
  'report'  => array(
    'title' => $labels[ 'report-form' ][ $lang ],
    'desc'  => $labels[ 'report-form-desc' ][ $lang ],
    'fields'    => array(
      'title'   => array(
        'type'        => 'input',
        'input_type'  => 'text',
        'label' 		  => $labels[ 'report-title' ][ $lang ],
		    'placeholder'	=> $labels[ 'optional' ][ $lang ],
        'name'        => 'title'
      ),
	    'date'  => array(
        'type'        => 'input',
        'input_type'  => 'date',
        'label'       => $labels[ 'report-date' ][ $lang ],
        'name'        => 'incident-date',
        'class'       => 'form-required'
      ),
      'description'  => array(
        'type'        => 'textarea',
        'label'       => $labels[ 'report-desc' ][ $lang ],
		    'placeholder'	=> $labels[ 'optional' ][ $lang ],
        'name'        => 'description'
      ),


    )
  ),
  'address' => array(
	  'title'	=> $labels[ 'address-form' ][ $lang ],
    'desc'  => $labels[ 'address-form-desc' ][ $lang ],
    'class' => 'form-address box',
    'fields' => array(
      'state' => array(
        'type'        => 'dropdown',
        'options'     => $states,
        'label'       => $labels[ 'state-title' ][ $lang ],
        'name'        => 'state',
        'class'       => 'form-required form-state',
        'placeholder' => $labels[ 'state-placeholder' ][ $lang ],
      ),

      'district'  => array(
        'type'        => 'dropdown',
        'options'     => $districts,
        'label'       => $labels[ 'district-title' ][ $lang ],
        'name'        => 'district',
        'class'       => 'form-required form-district',
        'placeholder' => $labels[ 'district-placeholder' ][ $lang ],

      ),
      'address' => array(
        'type'        => 'input',
        'input_type'  => 'text',
        'label'       => $labels[ 'address-title' ][ $lang ],
        'name'        => 'incident-address',
        'class'       => 'form-address-text',
        'placeholder' => $labels[ 'optional' ][ $lang ]
      ),
    ),
  ),
  'categories' => array(
    'class' 	=> '',
    'fields' 	=> array(
      'report-type'  => array(
		    'class'   => 'form-categories',
        'type'    => 'checkbox',
        'label'   => $labels[ 'report-type-title' ][ $lang ],
        'options' => $report_types,
        'name'    => 'report-type[]'
      ),
      'victims'  => array(
        'class'   => 'form-categories',
		    'type'    => 'checkbox',
        'label'   => $labels[ 'victims-title' ][ $lang ],
        'options' => $victims,
        'name'    => 'victims[]'
      ),
    ),
  ),


  'extra' => array(
    'title'	  => $labels[ 'extra-form-title' ][ $lang ],
    'desc'    => $labels[ 'extra-form-title' ][ $lang ],
    'class'	  => 'box',
    'fields'  => array(
      'images'  => array(
		    'class'       => 'form-images form-multi-fields',
        'type'        => 'multiple-fields',
        'fields_type' => 'multiple-image',
        'label'       => $labels[ 'images-title' ][ $lang ],
        'name'        => 'files'
      ),
      'links'  => array(
		    'class'       => 'form-links form-multi-fields',
        'type'        => 'multiple-fields',
        'fields_type' => 'multiple-text',
        'label'       => $labels[ 'links-title' ][ $lang ],
        'name'        => 'links[]'
      ),
    ),
  ),

  'contact-info'  => array(
    'class'		=> 'box',
	  'title'		=> $labels[ 'contact-form-title' ][ $lang ],
	  'desc'		=> $labels[ 'contact-form-desc' ][ $lang ],
    'fields'  => array(
        'name'  => array(
        'type'        => 'input',
        'input_type'  => 'text',
        'label'       => $labels[ 'contact-name-title' ][ $lang ],
        'name'        => 'contact-name',
        'placeholder' => $labels[ 'optional' ][ $lang ]
      ),
      'contact-type'  => array(
        'class'   => 'form-required',
        'type'    => 'checkbox',
        'label'   => $labels[ 'contact-type-title' ][ $lang ],
        'name'    => 'contact-type',
        'options' => array(
          array( 'slug' => 'contact-phone', 'title' => $labels[ 'option-phone' ][ $lang ] ),
          array( 'slug' => 'contact-email', 'title' => $labels[ 'option-email' ][ $lang ] )
        )
      ),
      'phone'  => array(
        'type'        => 'input',
        'input_type'  => 'number',
        'label'       => $labels[ 'contact-phone-title' ][ $lang ],
        'name'        => 'contact-phone',
        'class'       => 'form-required'
      ),
      'email'  => array(
        'type'        => 'input',
        'input_type'  => 'email',
        'label'       => $labels[ 'contact-email-title' ][ $lang ],
        'name'        => 'contact-email',
        'class'       => 'form-required'
      ),
    ),
  ),

);

echo "<form class='soah-fep' id='featured_upload' method='post' enctype='multipart/form-data'>";
if( !$_POST ){

  foreach ($form_sections as $section) {
    $contact_form->display_section( $section );
  }
  echo "<div class='g-recaptcha' data-sitekey='".SITE_KEY."'></div>";
  echo "<div class='form-alert error'></div>";
  echo "<input class='submit' name='submit' type='submit' value='Submit Report' />";

}
else{
  if( $form_success_flag ){ $message = "Report has been submitted successfully"; }
  else{ $message = "Report could not be submitted. The required fields were missing. Please try again."; }
  echo "<div style='margin-top:50px;' class='form-alert'>".$message."</div>";
  echo "<script>function refreshPage(){ window.location.href = window.location.href;} setTimeout( refreshPage, 5000 );</script>";
}

echo "</form>";
