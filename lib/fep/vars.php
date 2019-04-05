<?php


add_filter( 'soah-fep-labels', function( $labels ){
  $labels = array(
    'report-form' => array(
      'en'  => 'Report an incident',
      'hi'  => ''
    ),
    'report-form-desc'  => array(
      'en'  => 'Please use the form below to report an incident of violence and discrimination',
      'hi'  => ''
    ),
    'report-title' => array(
      'en'  => 'Give your report a title',
      'hi'  => ''
    ),
    'report-desc' => array(
      'en'  => 'Describe the incident in as much detail as possible',
      'hi'  => ''
    ),
    'report-date' => array(
      'en'  => 'Incident Date (required)',
      'hi'  => ''
    ),
    'optional'  => array(
      'en'  => 'This is optional',
      'hi'  => ''
    ),
    'address-form'  => array(
      'en'  => 'Where did this incident happen?',
      'hi'  => ''
    ),
    'address-form-desc' => array(
      'en'  => 'Please give as accurate details as possible',
      'hi'  => ''
    ),
    'state-title' => array(
      'en'  => 'Select State (required)',
      'hi'  => ''
    ),
    'state-placeholder' => array(
      'en'  => 'State',
      'hi'  => ''
    ),
    'district-title' => array(
      'en'  => 'Select District (required)',
      'hi'  => ''
    ),
    'district-placeholder' => array(
      'en'  => 'District',
      'hi'  => ''
    ),
    'address-title' => array(
      'en'  => 'Incident Address',
      'hi'  => ''
    ),
    'report-type-title' => array(
      'en'  => 'Categorize the incident as',
      'hi'  => ''
    ),
    'victims-title' => array(
      'en'  => 'Who all were the victims',
      'hi'  => ''
    ),
    'extra-form-title'  => array(
      'en'  => 'Additional information',
      'hi'  => ''
    ),
    'extra-form-desc'  => array(
      'en'  => 'Provides context and authenticity to the incident',
      'hi'  => ''
    ),
    'images-title'  => array(
      'en'  => 'Upload Images',
      'hi'  => ''
    ),
    'links-title'  => array(
      'en'  => 'Links to news article',
      'hi'  => ''
    ),
    'contact-form-title'  => array(
      'en'  => 'Contact Information',
      'hi'  => ''
    ),
    'contact-form-desc'  => array(
      'en'  => 'This information will be kept private and will only be needed for verification',
      'hi'  => ''
    ),
    'contact-name-title'  => array(
      'en'  => 'Contact Name',
      'hi'  => ''
    ),
    'contact-type-title'  => array(
      'en'  => 'How should we contact you? (required)',
      'hi'  => ''
    ),
    'option-phone'  => array(
      'en'  => 'Phone',
      'hi'  => ''
    ),
    'option-email'  => array(
      'en'  => 'Email',
      'hi'  => ''
    ),
    'contact-phone-title'  => array(
      'en'  => 'Contact Phone Number (required)',
      'hi'  => ''
    ),
    'contact-email-title'  => array(
      'en'  => 'Contact Email (required)',
      'hi'  => ''
    ),
  );

  return $labels;
});
