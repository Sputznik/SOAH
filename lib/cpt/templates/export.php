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
</div>
<?php

  if( $_POST ):

    // KEEP THE NAME OF THE FILE DYNAMIC
    $file_slug = 'mv-data-'.time();

    // NEED TO PASS THIS INFORMATION TO THE MODAL TO DOWNLOAD WHEN THE PROCESS IS COMPLETED
    $filePath = $this->getFilePath( $file_slug );

    // HAS TWO ITEMS IN THE ARRAY: TAX AND DATE
    $batch_params = $this->paramsToString( $_POST );

    // NEEDS BATCH PARAMS TO CREATE THE QUERY ARGS
    $posts_per_page = 100;
    $query_args = $this->queryArgs( $batch_params, $posts_per_page ); // GET THE QUERY ARGS
    $the_query = new WP_Query( $query_args );
    $total_posts = $the_query->found_posts;                 // TOTAL NUMBER OF POSTS FOUND IN THE QUERY ARGS PASSED
    $batches = (int) ( $total_posts / $posts_per_page );    // DYNAMICALLY CREATE THE NUMBER OF BATCHES

    // KEEPING THE CONTENTS URL SAFE SO THAT WE DON'T LOOSE ANY INFORMATION DURING THE TRANSFER
    if( isset( $batch_params['tax'] ) ){ $batch_params['tax'] = urlencode( $batch_params['tax'] ); }

    // ADDING THE FILE SLUG INTO THE PARMAETERS THAT NEEDS TO BE PASSED
    $batch_params['file_slug'] = $file_slug;
    $batch_params['posts_per_page'] = $posts_per_page;
  ?>


  <div id="modal-batch-process" class="modal fade" tabindex="-1" role="dialog" data-behaviour="export-modal" data-csv="<?php _e( $filePath['url'] );?>">
  	<div class="modal-dialog" role="document">
  		<div class="modal-content">
  			<div class="modal-body">
        <?php

          // PROGRESS BAR TO SHOW THE BATCH PROCESSING OF EXPORTING POSTS INTO A CSV FILE
          $this->batchProcess( array(
            'result'      => '',
            'title'	      => 'Please wait as the CSV is being exported.',
            'desc'			  => 'Make sure that your popups are enabled for this url or the browser will stop the download',
            'batches'		  => $batches,
            'btn_text' 		=> 'Export CSV',
            'batch_action'=> 'soah_export',
            'params'		  => $batch_params
          ) );
        ?>
  			</div>
  		</div>
  	</div>
  </div>
<?php endif;?>
