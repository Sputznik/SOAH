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
  /*
  function checkForEmpty( $el, el_type ){
    if( ( $el.val() == 0 && el_type == 'select' ) || ( $el.val() == "" && el_type == 'text' ) ){
      console.log('text');
      var temp = errorMessage( "Required fields are empty." );
      console.log( temp );
      return temp;
    }
    if( el_type == 'checkbox' ){
      var $parent = $el.closest('.form-required');
      var num_checked = $parent.find('input[type="checkbox"]:checked').length;

      if( num_checked <= 0 ){
        return errorMessage( "Required checkbox fields are empty." );
      }
    }
    return true;
  }
  */

  function formCheck( $slide ){

    var flag 	= true,
			fields 	= $slide.find(".form-required:not(.hide) input, .form-required select").serializeArray();

    // console.log( fields );

    $.each( fields, function( i, field ){
			if( !field.value || field.value == "0" ){
        // if(".form-required:not(.hide) input[type='number']" )
				errorMessage( "You have missed some required fields." );
				flag = false;
      }
      //Phone Number validation
      else if( field.name === 'contact-phone' ){
        if( field.value.length !=10 ){
          errorMessage( "Contact number must be a 10 digit number." );
  				flag = false;
        }
        else if( !( field.value >= 6000000000 && field.value <= 9999999999 ) ){
          errorMessage( "Contact number must be between 6000000000 and 9999999999." );
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
        errorMessage( "You have missed some required fields." );
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

    jQuery('.form-alert').hide();

    var response      = grecaptcha.getResponse(),
      responseLength  = response.length;

    if( responseLength == 0 ){
      errorMessage( "Please check the captcha to determine you are human" );
    }

  });

});
