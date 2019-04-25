<div data-atts='<?php _e( wp_json_encode( $atts ) );?>' style='margin-top:80px;' data-behaviour='choropleth-map'>
  <div class="loader">
    <h3><i class='fa fa-spinner fa-spin'></i> Loading data, please wait..</h3>
  </div>
  <div id="map"></div>

  <div class="title">
    <h1><?php _e( $atts['title'] );?></h1>
  </div>

  <div class="map_sidebar_open">
    <button type="button" id="filter_form_open" class="btn btn-warning btn-lg">
      <i class="fa fa-align-left"></i>
      <span>Filter Map</span>
    </button>
  </div>

  <div class="map_sidebar">
    <button type="button" id="map_sidebar_dismiss" class="btn btn-danger">
      <i class="fa fa-arrow-right"></i>
      <span>Hide this</span>
    </button>
    <h3>Filter Map</h3>
    <p>Use the form below to fliter the data on the map</p>
    <hr >
    <form>
      <label>Select State</label>
      <select></select>
      <br >

      <label>Select incident category</label>
      <input type="checkbox"> category name</input>
      <br >
      
      <button type="submit" class="btn btn-success">
        <i class="fa fa-check"></i>
        <span>Apply Filter</span>
      </button>
    </form>
  </div>

  <div class="map_overlay"></div>

</div>
<style>
  .header3 .affix{ z-index: 1999; }
</style>
