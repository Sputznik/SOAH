(function ($) {

	$.fn.choropleth_map = function(){

		return this.each(function() {

      var $el 			= jQuery( this ),
				$form				= $el.find('form'),
				data 				= [],
        atts  			= $el.data( 'atts' );

			// GLOBAL VARIABLES
			var map, gjLayerDist, gjLayerStates;

			//Filter form submit
			$form.on( 'submit', function( ev ){

				// PREVENT DEFAULT EVENT HANDLER
				ev.preventDefault();

				// GET AJAX DATA IN JSON FORM
				getData();

				// HIDE SIDEBAR
				hideSidebar();
			});

			function createMap(){
				//SETUP BASEMAP WITH BLANK DISTRICT LAYER
				map = L.map('map').setView( [22.27, 80.37], 5 );
				gjLayerDist = L.geoJson();
				gjLayerDist.addTo(map);

				//var hybUrl='https://api.mapbox.com/styles/v1/mapbox/outdoors-v9/tiles/256/{z}/{x}/{y}?access_token=pk.eyJ1IjoiZ3VuZWV0bmFydWxhIiwiYSI6IldYQUNyd0UifQ.EtQC56soqWJ-KBQqHwcpuw';
				var hybUrl = 'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}';
				var hybAttrib = 'ESRI World Light Gray | Map data © <a href="http://openstreetmap.org" target="_blank">OpenStreetMap</a> contributors & <a href="http://datameet.org" target="_blank">Data{Meet}</a>';
				var hyb = new L.TileLayer(hybUrl, {minZoom: 4, maxZoom: 8, attribution: hybAttrib, opacity:1}).addTo(map);
				L.control.scale().addTo(map);

				//ADD STATE BOUNDARIES
				var statelines = {
					"color":"#000",
					"weight":2,
					"opacity":1,
					"fill":false
				};

				gjLayerStates = L.geoJson( geoStates, { style: statelines } );
				gjLayerStates.addTo(map);

			}

			//REQUEST FOR DISTRICT WISE DATA
			function getData(){

				// SHOW THE LOADER
				$el.find('.loader').show();

				jQuery.ajax({
					'url'			: atts['url'],
					'data'		: $form.serialize(),
					'error'		: function(){ alert( 'Error has occurred' ); },
					'dataType'	: 'json',
					'success'	: function( json_data ){

						// HIDE THE LOADER
						$el.find('.loader').hide();

						data = json_data['data'];

						//REMOVE DISTRICTS LAYER FOR REFRESH
						map.removeLayer(gjLayerDist);

						// RENDER THE MAP IN THE CORRECT DOM
		        drawDistricts();

						// CREATE COLOR CODED KEYS
						createKeys( json_data['color_rules'] );

						// CREATE CONTEXT THAT GIVES MORE INFORMATION
						createContext( json_data['context'] );
					}
				});
			}



      function drawDistricts(){
				console.log(data);
        //ADD DISTRICT BOUNDARIES
        gjLayerDist = L.geoJson( geodist, { style: styledist, onEachFeature: onEachDist, filter: matchDistricts } );
        gjLayerDist.addTo( map );
				map.fitBounds( gjLayerDist.getBounds() );

				//ONLY ADD DISTRICTS THAT ARE AVAILABLE IN THE DATA
				function matchDistricts(feature) {
					for (var k = 0; k<data.length; k++) {
						if ( feature.properties["DISTRICT"] == data[k]["district"] && feature.properties["ST_NM"] == data[k]["state"]) return true;
					}
					return false;
				}

      }

			/*
			// USED INSIDE POP CONTENT
			// RETURNS STRING VERSION OF MULTIPLE SELECTED CHECKBOXES
			function getFormValues( form_name ){
				var $list = $el.find('form [name="' + form_name + '"]:checked');
				var total = $list.length;
				var list_str = "";

				$list.each( function( i ){
					var $current_report_type = jQuery(this);
					if( ( i == total-1 ) && ( i != 0 ) ){ list_str += " or "; }
					else if( ( i < total-1 ) && ( i != 0 ) ){ list_str += ", "; }
					list_str += $current_report_type.val();
				});
				return list_str;
			}
			*/

      function popContent( feature ) {

        //FOR DISTRICT POP UPS ON CLICK
        for ( var i = 0; i<data.length; i++ ){
          if ( data[i]["district"] == feature.properties["DISTRICT"] ) {

						var content = "<h4><a target='_blank' href='" + data[i]["url"] + "'>" + data[i]["district"] + ", " + data[i]["state"] + "</a></h4>";

						content += "<p><a target='_blank' href='" + data[i]["url"] + "'>" + data[i]["reports"];

						if( data[i]["reports"] == 1 ){ content += " incident"; }
						else{ content += " incidents"; }

						content += " (" + data[i]['percentile'] + "%)";

						//report_types = getFormValues( 'tax_report-type[]' );
						//year = $el.find('form [name=postdate_year]').val();
						//victims = getFormValues( 'tax_victims[]' );

						//if( year ){ content += " in <b>" + year +"</b>"; }

						content += " reported";

						//if( report_types ){ content += " for <b>" + report_types +"</b>"; }

						//if( victims ){ content += " on <b>" + victims + "</b>"; }

						content += " </a></p>";

            return content;
          }
        }
      }


      function styledist( feature ) {
				for ( var i = 0; i<data.length; i++ ){
					if ( data[i]["district"] == feature.properties["DISTRICT"] ) {
						return {
		          fillColor		: data[i]['color'],
		          weight			: 1,
		          opacity			: 0.4,
		          color				: 'black',
		          dashArray		: '1',
		          fillOpacity	: 0.8
		        };
          }
				}
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

			function hideSidebar(){
				// hide sidebar
        $el.find('.map_sidebar').removeClass('activated');

        // hide overlay
        $el.find('.map_overlay').removeClass('activated');
			}

			function showSidebar(){
				// open sidebar
        $el.find('.map_sidebar').addClass('activated');

        // fade in the overlay
        $el.find('.map_overlay').addClass('activated');

				$el.find('.collapse.in').toggleClass('in');
        $el.find('a[aria-expanded=true]').attr('aria-expanded', 'false');
			}

			$el.find('#map_sidebar_dismiss, .map_overlay').on('click', function () { hideSidebar(); });

      $el.find('#filter_form_open').on('click', function () { showSidebar(); });

			// CREATE A SECTION THAT EXPLAINS THE CONTEXT
			function createContext( context ){
				var $context = $el.find(".context");

				// REMOVE THE OLD DATA
				$context.html('');

				// BETWEEN RANGES
				jQuery.each( context, function( i, item ){
					addContextElement( item['label'] + ": <b>" + item['value'] + "</b>" );
				} );

				function addContextElement( text ){
					var $p = jQuery( document.createElement( 'p' ) );
					$p.html( text );
					$p.appendTo( $context );
				}
			}

			// CREATE COLOR CODED KEYS
			function createKeys( color_rules ){

				var $key 			= $el.find(".key");

				// REMOVE THE OLD DATA
				$key.html('');

				// BETWEEN RANGES
				jQuery.each( color_rules['ranges'], function( i, range ){
					if( i == 0 ){
						addKey( range['color'], "Lowest (<=" + range['max_value'] + " reports)" );
					}
					else{
						addKey( range['color'], "Between " + range['min_value'] + " and " + range['max_value'] + " reports" );
					}

				} );

				// MAX VALUE
				addKey( color_rules['max']['color'], "Highest (>=" + color_rules['max']['value'] + " reports)" );

				function addKey( color, text ){
					var $p = jQuery( document.createElement( 'p' ) );

					var $icon = jQuery( document.createElement( 'i' ) );
					$icon.css( { background: color } );
					$icon.appendTo( $p );

					var $span = jQuery( document.createElement( 'span' ) );
					$span.html( text );
					$span.appendTo( $p );

					$p.prependTo( $key );
				}

			}

      // INITIALIZE FUNCTION
      function init(){

				createMap();

				getData();





			}

      init();

    });
  };
}(jQuery));

jQuery(document).ready(function(){

  jQuery( '[data-behaviour~=choropleth-map]' ).choropleth_map();

});
