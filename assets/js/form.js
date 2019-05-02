jQuery(document).ready(function(){

  // Clones all districts from select
  var cloneDistrictElements = jQuery('select[name=district]').clone();

  jQuery( 'input[name="contact-type"]' ).each( function(){

    var $el = jQuery( this ),
      value = $el.val(),
      $text = jQuery( 'input[name="' + value + '"]' );

    // HIDE THE TEXTFIELDS
    $text.closest('div').addClass('hide');

    // CLICKING ON THE CHECKBOXES - TOGGLE THE TEXT FIELDS
    $el.click( function(){
      $text.closest('div').toggleClass('hide');
    });

  });

  jQuery('[data-behaviour~=multiple-fields]').each( function(){
    var $el = jQuery( this ),
      count = 0,
      field = $el.data('field');

    function init(){

      var $wrapperImage = jQuery( document.createElement('div') );
      $wrapperImage.addClass('wrapper');
      $wrapperImage.appendTo( $el );

      var $wrapperButton = jQuery( document.createElement('div') );
      $wrapperButton.addClass('wrapperButton');
      $wrapperButton.appendTo( $el );

      createAddButton();

      createSingleField();
    }

    function getImageInput(){
      var $input = jQuery( document.createElement('input') );
      $input.attr( 'type', 'file' );
      $input.attr( 'placeholder', field['label'] ? field['label'] :  "" );
      $input.attr( 'name', field['name'] + count );
      return $input;
    }

    function getTextBox(){
      var $input = jQuery( document.createElement('input') );
      $input.attr( 'type', 'text' );
      $input.attr( 'placeholder', field['label'] ? field['label'] :  "" );
      $input.attr( 'name', field['name'] );
      return $input;
    }

    function createSingleField(){

      var $parent = jQuery( document.createElement('div') );
      $parent.addClass('multi-field-wrapper');
      $parent.appendTo( $el.find('.wrapper') );

      $input = getTextBox();

      switch( field['fields_type'] ){
        case 'multiple-image':
          $input = getImageInput();
          break;
      }

      $input.appendTo( $parent );


    };

    function createAddButton(){

      var $btn = jQuery( document.createElement('button') );
      $btn.attr( 'type', 'button' );
      $btn.addClass('add-btn');
      $btn.html( field['btn_text'] ? field['btn_text'] : 'Add Another');
      $btn.appendTo( $el.find('.wrapperButton') );

      $btn.click( function(){
        count++;
        //checks the total number of file fields
        var countImage = jQuery('.multi-field-wrapper').length;
          if( count <= 4 ){
              createSingleField();
          }
      });

    };

    init();

  });

  /*
  //Add multiple image fields
  jQuery('[data-behaviour~=multiple-image]').each( function(){
    var imageCount = 0;     //imagecounter

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
  */

  //change districts when state is changed
  jQuery('select[name=state]').change( function( ev ){

    var $el = jQuery(ev.target);

    var currentState = $el.val();

    jQuery('select[name=district] option').remove();

    //Clones districts from cloneDistrictElements
    var options = cloneDistrictElements.find('option[data-state~=' + currentState + ']').clone();

    var defaultOption = cloneDistrictElements.find('option[value~=0]').clone()

    defaultOption.appendTo('select[name=district]');

    options.appendTo('select[name=district]');

    jQuery('select[name=district]').val(0);

  });


  jQuery('.form-alert.error').hide();

  function errorMessage( message ){
    event.preventDefault();
    jQuery('.form-alert').html( message );
    jQuery('.form-alert').show();
    return false;
  }


  function formCheck( $slide ){

    var flag 	    = true,
      errorText   = jQuery('.soah-fep').data('error'),
			fields 	    = $slide.find(".form-required:not(.hide) input, .form-required select").serializeArray();

    console.log( errorText );

    $.each( fields, function( i, field ){
			if( !field.value || field.value == "0" ){
        errorMessage( errorText['missed'] );
				flag = false;
      }
      //Phone Number validation
      else if( field.name === 'contact-phone' ){
        if( ( field.value.length !=10 ) || !( field.value >= 6000000000 && field.value <= 9999999999 ) ){
          errorMessage( errorText['contact-number'] );
  				flag = false;
        }

      }
		});

    // SEPERATE CASE FOR CHECKBOXES
    $slide.find( '.form-required input[type=checkbox]' ).each( function( i, el ){
      var $el       = jQuery( el ),
        $parent     = $el.closest('.form-required'),
        num_checked = $parent.find('input[type="checkbox"]:checked').length;

      if( num_checked <= 0 ){
        errorMessage( errorText['missed'] );
        flag = false;
      }

    });

    return flag;
  }

  // PARTIAL FORM VALIDATION EACH TIME THE NEXT BUTTON IS CLICKED
  jQuery('.soah-fep').on('meteor:beforeNextTransition', function( ev ){

    jQuery('.form-alert').hide();

    var $slide = jQuery( ev.target ),
					flag = formCheck( $slide );

    $slide.data('slide-disable', '1');

		if( flag ){ $slide.data('slide-disable', '0'); }

  });

  // VALIDATION ON THE FORM - CHECK FOR CAPTCHA
  jQuery('.soah-fep').on('submit',function(event){

    var errorText   = jQuery('.soah-fep').data('error');

    jQuery('.form-alert').hide();

    var response      = grecaptcha.getResponse(),
      responseLength  = response.length;

    if( responseLength == 0 ){
      errorMessage( errorText['captcha'] );
    }

  });

});
