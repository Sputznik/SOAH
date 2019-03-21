<?php

$contact_form = new FEP_FORM;


if(isset($_POST['submit'])){

  require_once( ABSPATH . 'wp-admin/includes/image.php' );
  require_once( ABSPATH . 'wp-admin/includes/file.php' );
  require_once( ABSPATH . 'wp-admin/includes/media.php' );


  // Add the content of the form to $post as an array
  $new_post = array(
    'post_title'    => isset( $_POST['title'] ) ? $_POST['title'] :  "",
    'post_content'  => isset( $_POST['description'] ) ? $_POST['description'] :  "",
    'post_date'     => isset($_POST['incident-date'] ) ? $_POST['incident-date'] :  "",
    'post_status'   => 'publish',           // Choose: publish, preview, future, draft, etc.
    'post_type'     =>  'reports'  // Use a custom post type if you want to
  );
  //save the new post and return its ID
  $post_id = wp_insert_post($new_post);

  // echo "<pre>";
  // print_r( $_POST );
  // echo "</pre>";

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



  // IMAGE UPLOAD
  $upload_overrides = array('test_form'=> false);
  foreach($_FILES as $key => $value){
    $movefile = media_handle_upload($key,$post_id,$upload_overrides);
  }

  // INSERTION OF CUSTOM FIELDS
  if( isset( $_POST['links'] ) ){
    $_POST['incident-links'] = implode( '\r\n', $_POST['links'] );
  }
  $metafields = array( 'contact-name', 'phone', 'email', 'incident-address', 'incident-links' );
  foreach( $metafields as $metafield ){
    if( isset( $_POST[ $metafield ] ) ){
      update_post_meta( $post_id, $metafield, $_POST[ $metafield ] );
    }
  }

  if( $movefile ){
    echo "<h3 style='color:green'>File was uploaded successfully</h3>";
  }
  else{
    echo 'error';
  }

}


/* GETTING STATES AND DISTRICTS FROM THE DB */
$locations  =   get_terms('locations',array(
  'hide_empty' => false
));



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
    'fields' => array(
      'title'  => array(
        'type'  => 'text',
        'label' 		=> 'Give your report a title',
		'placeholder'	=> 'This is optional',
        'name'  => 'title'
      ),
	  'date'  => array(
        'type'    => 'datepicker',
        'label'   => 'Incident Date',
        'name'    => 'incident-date'
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
    'class' => 'grid-2 box',
    'fields' => array(
      'state' => array(
        'type'    => 'dropdown',
        'options' => $states,
        'label'   => 'Select State',
        'name'    => 'state'
      ),

      'district'  => array(
        'type'    => 'dropdown',
        'options' => $districts,
        'label'   => 'Select District',
        'name'    => 'district'
      ),
      'address' => array(
        'type'  => 'text',
        'label' => 'Incident Address',
        'name'  => 'incident-address'
      ),
    ),
  ),
  'categories' => array(
    'class' 	=> 'grid-2',
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
        'name'  => 'contact-name'
      ),
      'contact-type'  => array(
        'type'  => 'checkbox',
        'label' => 'How should we contact you?',
        'name'  => '',
        'options' => array(
          array( 'slug' => 'phone', 'title' => 'Phone' ),
          array( 'slug' => 'email', 'title' => 'Email' )
        )
      ),
      'phone'  => array(
        'type'  => 'text',
        'label' => 'Contact Phone Number',
        'name'  => 'phone'
      ),
      'email'  => array(
        'type'  => 'text',
        'label' => 'Contact Email',
        'name'  => 'email'
      ),
    ),
  ),

);

?>

<form class="soah-fep" id="featured_upload" method="post" action="#" enctype="multipart/form-data" class="">
	<h2>Report an incident</h2>
	<p>Please use the form below to report an incident of violence and discrimination</p>
<?php
  foreach ($form_sections as $section) {
    $contact_form->display_section( $section );
  }
?>
  <input class="btn btn-primary sub" name="submit" type="submit" value="Upload Report" />
</form>