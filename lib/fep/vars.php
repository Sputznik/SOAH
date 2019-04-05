<?php


add_filter( 'soah-fep-labels', function( $labels ){
  $labels = array(
    'report-form' => array(
      'en'  => 'Report an incident',
      'hi'  => 'किसी घटना की रिपोर्ट करें'
    ),
    'report-form-desc'  => array(
      'en'  => 'Please use the form below to report an incident of violence and discrimination',
      'hi'  => 'हिंसा और भेदभाव की घटना की रिपोर्ट करने के लिए कृपया नीचे दिए गए फॉर्म का उपयोग करें'
    ),
    'report-title' => array(
      'en'  => 'Give your report a title',
      'hi'  => 'अपनी रिपोर्ट को एक शीर्षक दें'
    ),
    'report-desc' => array(
      'en'  => 'Describe the incident in as much detail as possible',
      'hi'  => 'घटना का यथासंभव वर्णन करें'
    ),
    'report-date' => array(
      'en'  => 'Incident Date (required)',
      'hi'  => 'घटना दिनांक (आवश्यक)'
    ),
    'optional'  => array(
      'en'  => 'This is optional',
      'hi'  => 'यह वैकल्पिक है'
    ),
    'address-form'  => array(
      'en'  => 'Where did this incident happen?',
      'hi'  => 'यह घटना कहां हुई?'
    ),
    'address-form-desc' => array(
      'en'  => 'Please give as accurate details as possible',
      'hi'  => 'कृपया यथासंभव सटीक विवरण दें'
    ),
    'state-title' => array(
      'en'  => 'Select State (required)',
      'hi'  => 'राज्य चुनें (आवश्यक)'
    ),
    'state-placeholder' => array(
      'en'  => 'State',
      'hi'  => 'राज्य'
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