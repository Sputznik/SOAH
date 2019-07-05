<div data-atts='<?php _e( wp_json_encode( $atts ) );?>' style='overflow:hidden;' data-behaviour='choropleth-map'>
  <div class="loader"><h3><i class='fa fa-spinner fa-spin'></i> Loading data, please wait..</h3></div>

  <div id="map"></div>

  <div class="map_sidebar_open">
    <button type="button" id="filter_form_open" class="btn btn-warning btn-lg">
      <i class="fa fa-filter"></i>
      <span>Filter Map</span>
    </button>
  </div>

  <div class="map_sidebar">
    <button type="button" id="map_sidebar_dismiss" class="btn btn-danger"><i class="fa fa-arrow-right"></i><span>Hide this</span></button>
    <h3>Filter Map</h3>
    <p>Use the form below to filter the data on the map</p>
    <hr >
    <form>
      <div class="grid-col2">
        <?php
          echo do_shortcode('[orbit_filter label="Select State" type=tax form=bt_dropdown_checkboxes typeval=locations tax_parent=0 tax_hide_empty=false]');
        ?>
      </div>
      <div class="grid-col2">
        <?php
          echo do_shortcode('[orbit_filter label="Select Victims" type=tax form=bt_dropdown_checkboxes typeval=victims tax_hide_empty=false]');
          echo do_shortcode('[orbit_filter label="Select Report Type" type=tax form=bt_dropdown_checkboxes typeval=report-type tax_hide_empty=false]');
        ?>
      </div>
      <div class="grid-col2">
        <?php
          echo do_shortcode('[orbit_filter label="From" type=postdate typeval=after form=date]');
          echo do_shortcode('[orbit_filter label="To" type=postdate typeval=before form=date]');
          echo do_shortcode('[orbit_filter label="Verified" type=tax form=checkbox typeval="meta-info" tax_hide_empty=false]');
        ?>
      </div>
      <button type="submit" class="btn btn-success"><i class="fa fa-check"></i><span>Apply Filter</span></button>
      <span class="or-text">or</span>
      <button type="button" class="btn btn-reset reset">Reset</span></button>
    </form>
    <br/>

  </div>

  <div class="map_overlay"></div>

  <div class="map-info">
    <button type="button" class="btn info-btn">
      <i class="fa fa-info"></i>
    </button>
    <div class='map-box'>
      <button class='close-btn'>&times;</button>
      <div class="context">
        <h5>District-wise Reports</h5>
        <div class='context-info'></div>
      </div>
      <div class="key"></div>
    </div>
  </div>
  <!-- Map Icons -->
  <!-- <ul class="list-inline" id="icon-holder">
    <li><a class="map-icon btn info" id="m-info"><i class="fa fa-info"></i></a></li>
    <li><a class="map-icon btn filter" id="m-filter"><i class="fa fa-filter"></i></a></li>
    <li><a class="map-icon btn scroll" id="m-scrollBtn"><i class="fa fa-arrow-down"></i></a></li>
  </ul> -->
</div>
<!-- <div id="map-end"></div> -->

<style>
  .header3 .affix{ z-index: 1999; }
</style>
