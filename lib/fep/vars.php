<?php


add_filter( 'soah-fep-labels', function( $labels ){
  $labels = array(
    'error-missed' => array(
      'en'  => 'You have missed some required fields.',
      'hi'  => 'कुछ आवश्यक फ़ील्ड छूट गए हैं'
    ),
    'error-contact-number' => array(
      'en'  => 'Contact number must be a 10 digit number and must be between 6000000000 and 9999999999.',
      'hi'  => 'संपर्क नंबर मान्य नहीं है'
    ),
    'error-captcha' => array(
      'en'  => 'Please check the captcha to determine you are human.',
      'hi'  => 'कृपया यह निर्धारित करने के लिए कैप्चा जांचें कि आप मानव हैं।'
    ),
    'add-another' => array(
      'en'  => 'Add Another',
      'hi'  => 'एक और'
    ),
    'previous' => array(
      'en'  => 'Previous',
      'hi'  => 'पिछला'
    ),
    'next' => array(
      'en'  => 'Next',
      'hi'  => 'अगला'
    ),
    'submit' => array(
      'en'  => 'Submit',
      'hi'  => 'जमा करें'
    ),
    'report-form' => array(
      'en'  => 'Report an incident',
      'hi'  => 'किसी घटना की रिपोर्ट करें'
    ),
    'report-form-desc'  => array(
      'en'  => 'Please use the form below to report an incident of violence and discrimination. All the required fields are marked with <span class="red">*</span>',
      'hi'  => 'हिंसा और भेदभाव की घटना की रिपोर्ट करने के लिए कृपया नीचे दिए गए फॉर्म का उपयोग करें। सभी आवश्यक फ़ील्ड <span class="red">*</span> के साथ चिह्नित हैं'
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
      'en'  => 'Incident Date <span class="red">*</span>',
      'hi'  => 'घटना दिनांक <span class="red">*</span>'
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
      'en'  => 'Select State <span class="red">*</span>',
      'hi'  => 'राज्य चुनें <span class="red">*</span>'
    ),
    'state-placeholder' => array(
      'en'  => 'State',
      'hi'  => 'राज्य'
    ),
    'district-title' => array(
      'en'  => 'Select District <span class="red">*</span>',
      'hi'  => 'जिला चुनें <span class="red">*</span>'
    ),
    'district-placeholder' => array(
      'en'  => 'District',
      'hi'  => 'जिला'
    ),
    'address-title' => array(
      'en'  => 'Incident Address',
      'hi'  => 'घटना का पता'
    ),
    'report-type-title' => array(
      'en'  => 'Did the incident involve any of the following',
      'hi'  => 'घटना को वर्गीकृत करें'
    ),
    'victims-title' => array(
      'en'  => 'Who all were the victims',
      'hi'  => 'जो सभी पीड़ित थे'
    ),
    'extra-form-title'  => array(
      'en'  => 'Additional information (optional)',
      'hi'  => 'अतिरिक्त जानकारी'
    ),
    'extra-form-desc'  => array(
      'en'  => 'Provides context and authenticity to the incident',
      'hi'  => 'घटना को संदर्भ और प्रामाणिकता प्रदान करता है'
    ),
    'images-title'  => array(
      'en'  => 'Upload Images',
      'hi'  => 'तश्वीरें अपलोड करो'
    ),
    'links-title'  => array(
      'en'  => 'Links to news article',
      'hi'  => 'समाचार लेख के लिंक'
    ),
    'contact-form-title'  => array(
      'en'  => 'Your Contact Information',
      'hi'  => 'संपर्क जानकारी'
    ),
    'contact-form-desc'  => array(
      'en'  => 'This information will be kept private and will only be needed for verification',
      'hi'  => 'यह जानकारी निजी रखी जाएगी और केवल सत्यापन के लिए आवश्यक होगी'
    ),
    'contact-name-title'  => array(
      'en'  => 'Contact Name',
      'hi'  => 'संपर्क नाम'
    ),
    'contact-type-title'  => array(
      'en'  => 'How should we contact you? <span class="red">*</span>',
      'hi'  => 'हमें कैसे आप से संपर्क करना चाहिए? <span class="red">*</span>'
    ),
    'option-phone'  => array(
      'en'  => 'Phone',
      'hi'  => 'फ़ोन'
    ),
    'option-email'  => array(
      'en'  => 'Email',
      'hi'  => 'ईमेल'
    ),
    'contact-phone-title'  => array(
      'en'  => 'Contact Phone Number <span class="red">*</span>',
      'hi'  => 'संपर्क फ़ोन नंबर <span class="red">*</span>'
    ),
    'contact-email-title'  => array(
      'en'  => 'Contact Email <span class="red">*</span>',
      'hi'  => 'संपर्क ईमेल <span class="red">*</span>'
    ),
  );
  return $labels;
});
