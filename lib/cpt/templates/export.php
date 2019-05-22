<div class="" style="max-width:650px; margin-left:auto; margin-right:auto;">
  <form method="POST">
    <h3>Download Reports</h3>
    <hr>
    <div class="grid-col2">
    <?php
      echo do_shortcode('[orbit_filter label="Which Year" type=postdate typeval=year form=dropdown]');
      echo do_shortcode('[orbit_filter label="Select State" type=tax form=dropdown typeval=locations tax_parent=0 tax_hide_empty=false]');
      echo do_shortcode('[orbit_filter label="Select Report Type" type=tax form=bt_dropdown_checkboxes typeval=report-type tax_hide_empty=false]');
      echo do_shortcode('[orbit_filter label="Select Victims" type=tax form=bt_dropdown_checkboxes typeval=victims tax_hide_empty=false]');
    ?>
    </div>
    <p>&nbsp;</p>
    <p></p>
    <p><button class="btn btn-default" type="submit">Download</button></p>
  </form>


<?php

  if( $_POST ){

    $orbit_util = ORBIT_UTIL::getInstance();

    $batch_params = $orbit_util->paramsToString( $_POST );

    if( isset( $batch_params['tax'] ) ){
      $batch_params['tax'] = urlencode( $batch_params['tax'] );
    }

    $batch_params['file_slug'] = 'sam';//time();

    $batch_process = ORBIT_BATCH_PROCESS::getInstance();

    echo $batch_process->plain_shortcode( array(
      'title'	      => 'Please wait as the CSV is being exported.',
      'desc'			  => '',
      'batches'		  => 2,
      'btn_text' 		=> 'Export CSV',
      'batch_action'=> 'soah_export',
      'params'		  => $batch_params
    ) );

  }


?>
</div>
