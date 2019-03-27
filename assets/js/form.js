jQuery(document).ready(function(){

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

    jQuery('select[name=district] option').hide();
    jQuery('select[name=district] option[data-state~=' + currentState + ']').show();

    jQuery('select[name=district]').val(0);

    // console.log($el.val());
  });

  jQuery('.form-alert.error').hide();

  // VALIDATION ON THE FORM
  jQuery('.soah-fep').on('submit',function(event){

    jQuery('.form-alert').hide();

    function errorMessage( message ){
      event.preventDefault();
      jQuery('.form-alert').html( message );
      jQuery('.form-alert').show();
    }

    function checkForEmpty( $el, el_type ){
      if( ( $el.val() == 0 && el_type == 'select' ) || ( $el.val() == "" && el_type == 'text' ) ){
        errorMessage( "Required fields are empty." );
      }
      if( el_type == 'checkbox' ){
        var $parent = $el.closest('.form-required');
        var num_checked = $parent.find('input[type="checkbox"]:checked').length;

        if( num_checked <= 0 ){
          errorMessage( "Required fields are empty. Checkbox" );
        }
      }

    }
    jQuery( '.form-required select' ).each( function( i, el ){
      checkForEmpty( jQuery( el ), 'select' );
    });

    jQuery( '.form-required input' ).each( function( i, el ){
      if( !jQuery( el ).closest('div').hasClass('hide') ){
        var type = jQuery( el ).attr('type');
        checkForEmpty( jQuery( el ), type );
      }
    });

    var response      = grecaptcha.getResponse(),
      responseLength  = response.length;
    if( responseLength == 0 ){
      errorMessage( "Please check the captcha to determine you are human" );
    }

  });

});
