<div data-atts='<?php _e( wp_json_encode( $atts ) );?>' style='margin-top:80px;' data-behaviour='choropleth-map'>
  <div class="loader">
    <h3><i class='fa fa-spinner fa-spin'></i> Loading data, please wait..</h3>
  </div>
  <div id="map"></div>
  <div class="title">
    <h1><?php _e( $atts['title'] );?></h1>
  </div>
</div>
<style>
  .header3 .affix{ z-index: 1999; }
</style>
