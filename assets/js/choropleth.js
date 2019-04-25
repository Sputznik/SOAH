(function ($) {

	$.fn.choropleth_map = function(){

		return this.each(function() {

      var $el 			= jQuery( this ),
				data 				= [],
        atts  			= $el.data( 'atts' );


			function getData(){
				jQuery.ajax({
					'url'			: atts['url'],
					'error'		: function(){ alert( 'Error has occurred' ); },
					//'data'		: data,
					'dataType'	: 'json',
					'success'	: function( json_data ){

						data = json_data;


						// RENDER THE MAP IN THE CORRECT DOM
		        drawMap();

					}
				});
			}



      function drawMap(){

        // HIDE THE LOADER
        $el.find('.loader').hide();

        //SETUP BASEMAP
        var map = L.map('map').setView( [22.27, 80.37], 5 );

        //var hybUrl='https://api.mapbox.com/styles/v1/mapbox/outdoors-v9/tiles/256/{z}/{x}/{y}?access_token=pk.eyJ1IjoiZ3VuZWV0bmFydWxhIiwiYSI6IldYQUNyd0UifQ.EtQC56soqWJ-KBQqHwcpuw';
        var hybUrl = 'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}';
        var hybAttrib = 'ESRI World Light Gray | Map data © <a href="http://openstreetmap.org" target="_blank">OpenStreetMap</a> contributors & <a href="http://datameet.org" target="_blank">Data{Meet}</a>';
        var hyb = new L.TileLayer(hybUrl, {minZoom: 2, maxZoom: 18, attribution: hybAttrib, opacity:1}).addTo(map);

        //ADD DISTRICT BOUNDARIES
        var gjLayerDist = L.geoJson( geodist, { style: styledist, onEachFeature: onEachDist } );
        gjLayerDist.addTo(map);

        //ADD STATE BOUNDARIES
        var statelines = {
          "color":"#000",
          "weight":1,
          "opacity":1,
          "fill":false
        };

        var gjLayerStates = L.geoJson( geoStates, { style: statelines } );
        gjLayerStates.addTo(map);
      }

      function popContent( feature ) {
        //FOR DISTRICT POP UPS ON CLICK
        for ( var i = 0; i<data.length; i++ ){
          if ( data[i]["district"] == feature.properties["DISTRICT"] ) {
            return '<h4>'+data[i]["district"]+', '+data[i]["state"]+'</h4><p>' + data[i]["reports"] + ' incidents reported.</p>';
          }
        }
      }

      function styledist( feature ) {

				var color_rules = atts['color_rules'];

        //CHOROPLETH COLORS BASED ON RANGE ONLY
        var color = color_rules['default'];

				for ( var i = 0; i<data.length; i++ ){

					if ( data[i]["district"] == feature.properties["DISTRICT"] ) {

						// CONDITION IF THE VALUE IS BEYOND THE MIN AND MAX VALUE
						if ( data[i]["reports"] > color_rules['max']['value'] || data[i]["reports"] > color_rules['min']['value'] ){
							color = color_rules['min']['color'];
							if( data[i]["reports"] > color_rules['max']['value'] ){
								color = color_rules['max']['color'];
							}
						}
						else{
							// CONDITION WHEN THE VALUE IS BETWEEN THE MIN AND MAX RANGES
							jQuery.each( color_rules['ranges'], function( i, range ){
								if ( data[i]["reports"] >= range['min_value'] && data[i]["reports"] <= range['max_value'] ){
									color = range['color'];
								}
							} );
						}

          }
        }

				//console.log( color );

        return {
          fillColor		: color,
          weight			: 1,
          opacity			: 0.4,
          color				: 'black',
          dashArray		: '1',
          fillOpacity	: 0.8
        };
      }

      function onEachDist( feature, layer ) {
        //CONNECTING TOOLTIP AND POPUPS TO DISTRICTS
        layer.on({
          mouseover: highlightFeature,
          mouseout: resetHighlight
          //click: zoomToFeature
        });
        layer.bindTooltip( feature.properties.DISTRICT + ', ' + feature.properties.ST_NM, {
          direction : 'auto',
          className : 'statelabel',
          permanent : false,
          sticky    : true
        } );
        layer.bindPopup( popContent(feature), {maxWidth:600} );
      }

      function highlightFeature(e) {
        //DISTRICT HIGHLIGHT ON MOUSEOVER
        var layer = e.target;

        layer.setStyle( {
					color:'yellow',
					weight:3,
					opacity:0.9
				} );
        if ( !L.Browser.ie && !L.Browser.opera ) {
          layer.bringToFront();
        }
      }

      function resetHighlight(e) {
          //RESET HIGHLIGHT ON MOUSEOUT
          var layer = e.target;
          layer.setStyle({
            color:'black',
						weight:1,
						opacity:0.4
          });
      }

      function zoomToFeature(e) {
        // PROBABLY THE MAP VARIABLE NEEDS TO BE A GLOBAL VARIABLE HERE
        map.fitBounds(e.target.getBounds());
      }

			$('#map_sidebar_dismiss, .map_overlay').on('click', function () {
            // hide sidebar
            $('.map_sidebar').removeClass('activated');
            // hide overlay
            $('.map_overlay').removeClass('activated');
        });

        $('#filter_form_open').on('click', function () {
						console.log('clicked it');
            // open sidebar
            $('.map_sidebar').addClass('activated');
            // fade in the overlay
            $('.map_overlay').addClass('activated');
            $('.collapse.in').toggleClass('in');
            $('a[aria-expanded=true]').attr('aria-expanded', 'false');
        });

      // INITIALIZE FUNCTION
      function init(){


				getData();


      }

      init();

    });
  };
}(jQuery));

jQuery(document).ready(function(){

  jQuery( '[data-behaviour~=choropleth-map]' ).choropleth_map();

});
