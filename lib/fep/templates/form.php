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
      $_POST['incident-links'] = implode( '\r\n', $_POST['links'] );
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


/* GETTING STATES AND DISTRICTS FROM THE DB */
$locations  =   get_terms('locations',array( 'hide_empty' => false ));
$states = array();
$districts = array();
foreach( $locations as $location ){

  $temp = array(
    'slug' => $location->term_id,
    'title' => $location->name,
    'parent'  =>  $location->parent
  );
  if( $location->parent == 0 ){
    array_push( $states, $temp );
  }
  else{
    array_push( $districts, $temp );
  }
}
/* GETTING STATES AND DISTRICTS FROM THE DB */

/* REPORT TYPES FROM THE DB */
$report_types = $contact_form->getOptionsFromTaxonomy( 'report-type' );
$victims = $contact_form->getOptionsFromTaxonomy( 'victims' );
/* REPORT TYPES FROM THE DB */

$form_sections = array(
  'report'  => array(
    'title' => 'Report an incident',
    'desc'  => 'Please use the form below to report an incident of violence and discrimination',
    'fields' => array(
      'title'  => array(
        'type'  => 'text',
        'label' 		=> 'Give your report a title',
		'placeholder'	=> 'This is optional',
        'name'  => 'title'
      ),
	  'date'  => array(
        'type'    => 'datepicker',
        'label'   => 'Incident Date (required)',
        'name'    => 'incident-date',
        'class' => 'form-required'
      ),
      'description'  => array(
        'type'  => 'textarea',
        'label' => 'Describe the incident in as much detail as possible',
		'placeholder'	=> '',
        'name'  => 'description'
      ),


    )
  ),
  'address' => array(
	'title'	=> 'Where did this incident happen?',
    'class' => 'form-address box',
    'fields' => array(
      'state' => array(
        'type'    => 'dropdown',
        'options' => $states,
        'label'   => 'Select State (required)',
        'name'    => 'state',
        'class'   => 'form-required form-state'
      ),

      'district'  => array(
        'type'    => 'dropdown',
        'options' => $districts,
        'label'   => 'Select District (required)',
        'name'    => 'district',
        'class'   => 'form-required form-district'
      ),
      'address' => array(
        'type'  => 'text',
        'label' => 'Incident Address',
        'name'  => 'incident-address',
        'class' => 'form-address-text'
      ),
    ),
  ),
  'categories' => array(
    'class' 	=> '',
    'fields' 	=> array(
      'report-type'  => array(
		'class' => 'form-categories',
        'type'  => 'checkbox',
        'label' => 'Categorize the incident as',
        'options' => $report_types,
        'name'    => 'report-type[]'
      ),
      'victims'  => array(
        'class' => 'form-categories',
		'type'  => 'checkbox',
        'label' => 'Who all were the victims',
        'options' => $victims,
        'name'    => 'victims[]'
      ),
    ),
  ),


  'extra' => array(
    'title'	=> 'Additional information',
    'class'	=> 'grid-2',
    'fields' => array(
      'images'  => array(
		'class' => 'form-images form-multi-fields',
        'type'  => 'file',
        'label' => 'Upload Images',
        'name'  => 'files'
      ),
      'links'  => array(
		'class' => 'form-links form-multi-fields',
        'type'  => 'multiple-text',
        'label' => 'Links to news article',
        'name'  => 'links[]'
      ),
    ),
  ),

  'contact-info'  => array(
	'class'		=> 'box',
	'title'		=> 'Contact Information',
	'desc'		=> 'This information will be kept private and will only be needed for verification',
    'fields'  => array(
      'name'  => array(
        'type'  => 'text',
        'label' => 'Contact Name',
        'name'  => 'contact-name',
      ),
      'contact-type'  => array(
        'type'  => 'checkbox',
        'label' => 'How should we contact you?',
        'name'  => 'contact-type',
        'options' => array(
          array( 'slug' => 'contact-phone', 'title' => 'Phone' ),
          array( 'slug' => 'contact-email', 'title' => 'Email' )
        )
      ),
      'phone'  => array(
        'type'  => 'number',
        'label' => 'Contact Phone Number (required)',
        'name'  => 'contact-phone',
        'class' => 'form-required'
      ),
      'email'  => array(
        'type'  => 'email',
        'label' => 'Contact Email (required)',
        'name'  => 'contact-email',
        'class' => 'form-required'
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
  echo "<script>function refreshPage(){ window.location.href = window.location.href;} setTimeout( refreshPage, 1000 );</script>";
}

echo "</form>";
