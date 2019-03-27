jQuery(document).ready(function(){
  //hide the the div containing #phone and #email
  jQuery('#phone , #email').closest('div').hide();

  //toggle if checkbox is checked
  jQuery('input[type="checkbox"]').click(function(){

    var $checkValue = jQuery(this).val();
        if( $checkValue=='phone' || $checkValue=='email' )
      jQuery('#'+$checkValue).parent('.form-field').toggle();

  });


  //Add multiple image fields
  jQuery('[data-behaviour~=multiple-image]').each( function(){
    var imageCount = 0;//imagecounter

    var $wrapperImage = jQuery( document.createElement('div') );
    $wrapperImage.addClass('wrapperImage');
    $wrapperImage.appendTo( this );

    var $wrapperButton = jQuery( document.createElement('div') );
    $wrapperButton.addClass('wrapperButton');
    $wrapperButton.appendTo( this );

    var $el = jQuery( $wrapperImage );
    var $el2 = jQuery( '[data-behaviour~=multiple-image]' );

    function createImageField(){

      var $parent = jQuery( document.createElement('div') );
      $parent.addClass('multi-image-wrapper');
      $parent.appendTo( $el );

      var $input = jQuery( document.createElement('input') );
      $input.attr( 'type', 'file' );
      $input.attr( 'placeholder', $el2.attr('data-label') );
      $input.attr( 'name', $el2.attr('data-name') + imageCount);
      $input.appendTo( $parent );

    };

    function createAddImage(){

      var $btn = jQuery( document.createElement('button') );
      $btn.html( '+' );
      $btn.attr( 'type', 'button' );
      $btn.addClass('add-btn');
      $btn.html('Add Another');
      $btn.appendTo( $wrapperButton );

      $btn.click( function(){
        imageCount++;
        //checks the total number of file fields
        var countImage = jQuery('.multi-image-wrapper').length;
          if(countImage <= 4 ){
              createImageField();
          }
      });

    };

    createAddImage();

    createImageField();

  });

  //For adding multiple textfield for links
  jQuery('[data-behaviour~=multiple-text]').each( function(){

    var $wrapperLink = jQuery( document.createElement('div') );
    $wrapperLink.addClass('wrapperLink');
    $wrapperLink.appendTo( this );

    var $linkButton = jQuery( document.createElement('div') );
    $linkButton.addClass('linkButton');
    $linkButton.appendTo( this );

    var $el = jQuery( $wrapperLink );
    var $el2 = jQuery('[data-behaviour~=multiple-text]');
    function createTextBox(){

      var $parent = jQuery( document.createElement('div') );
      $parent.addClass('multi-text-wrapper');
      $parent.appendTo( $el );

      var $input = jQuery( document.createElement('input') );
      $input.attr( 'type', 'text' );
      $input.attr( 'placeholder', $el2.attr('data-label') );
      $input.attr( 'name', $el2.attr('data-name') );
      $input.appendTo( $parent );

    };

    function createAddButton(){

      var $btn = jQuery( document.createElement('button') );
      $btn.html( '+' );
      $btn.attr( 'type', 'button' );
      $btn.addClass('add-btn');
      $btn.html('Add Another');
      $btn.appendTo( $linkButton );

      $btn.click( function(){
        //checks the total number of file fields
        var countLink = jQuery('.multi-text-wrapper').length;
          if(countLink <= 4 ){
              createTextBox();
          }
      });

    };

    createAddButton();

    createTextBox();

  });

  //change districts when state is changed
  jQuery('select[name=state]').change( function( ev ){

    var $el = jQuery(ev.target);

    var currentState = $el.val();

    jQuery('select[name=district] option').hide();
    jQuery('select[name=district] option[data-state~=' + currentState + ']').show();

    jQuery('select[name=district]').val(0);

    // console.log($el.val());
  });

  //Check whether the captcha is selected or not
  jQuery('.soah-fep').on('submit',function(event){

    // Check whether the required fields are empty or not
    jQuery('.form-required input').each(function(i,el){
      if(jQuery( el ).val()==""){
        jQuery(this).after('<span style="color:red">Field Required</span>');
      }
    });


    jQuery('.form-required select').each(function(i,el){
      if(jQuery( el ).val()==0){
        jQuery(this).after('<span style="color:red">Field Required</span>');
      }

    });

    var response = grecaptcha.getResponse();
    var responseLength = response.length;
    if(responseLength == 0){
      event.preventDefault();
      alert('Please check the Captcha');
    }
  });

});
