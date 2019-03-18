jQuery(document).ready(function(){
//hide the the div containing #phone and #email
jQuery('#phone , #email').closest('div').hide();



  //check whether the fields are empty or not
  // jQuery('.sub').click(function(){
  //   var $title = jQuery('.title').val();
  //   var $description = jQuery('.des').val();
  //
  //   if($title == ""){
  //     alert('Title cannot be empty ');
  //     return false;
  //   }
  //
  //   if($description == ""){
  //     alert('Description cannot be empty');
  //     return false;
  //   }
  // });

  //toggle if checkbox is checked
  jQuery('input[type="checkbox"]').click(function(){

    var $checkValue = jQuery(this).val();
        if( $checkValue=='phone' || $checkValue=='email' )
      jQuery('#'+$checkValue).parent('.form-field').toggle();

  });

  //Add multiple image fields
  jQuery('[data-behaviour~=multiple-image]').each( function(){
    var imageCount = 0;//imagecounter
    var $el = jQuery( this );

    function createImageField(){

      var $parent = jQuery( document.createElement('div') );
      $parent.addClass('multi-text-wrapper');
      $parent.appendTo( $el );

      var $input = jQuery( document.createElement('input') );
      $input.attr( 'type', 'file' );
      $input.attr( 'placeholder', $el.attr('data-label') );
      $input.attr( 'name', $el.attr('data-name') + imageCount);
      $input.appendTo( $parent );

    };

    function createAddImage(){

      var $btn = jQuery( document.createElement('button') );
      $btn.html( '+' );
      $btn.attr( 'type', 'button' );
      $btn.addClass('add-btn');
      $btn.appendTo( $el );

      $btn.click( function(){
        imageCount++;
        createImageField();
      });

    };

    createAddImage();

    createImageField();

  });

  //For adding multiple textfield for links
  jQuery('[data-behaviour~=multiple-text]').each( function(){

    var $el = jQuery( this );

    function createTextBox(){

      var $parent = jQuery( document.createElement('div') );
      $parent.addClass('multi-text-wrapper');
      $parent.appendTo( $el );

      var $input = jQuery( document.createElement('input') );
      $input.attr( 'type', 'text' );
      $input.attr( 'placeholder', $el.attr('data-label') );
      $input.attr( 'name', $el.attr('data-name') );
      $input.appendTo( $parent );

    };

    function createAddButton(){

      var $btn = jQuery( document.createElement('button') );
      $btn.html( '+' );
      $btn.attr( 'type', 'button' );
      $btn.addClass('add-btn');
      $btn.appendTo( $el );

      $btn.click( function(){
        createTextBox();
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


});
