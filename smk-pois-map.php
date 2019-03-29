<?php

/*

Plugin Name: SMK Pois Map

Plugin URI: https://smk.com.co/

Description: Pois map block

Author: SMK

Version: 1.0.0

Author URI: https://smk.com.co/

*/

/*** ACF Maps API Key ***/

function my_acf_init() {

    acf_update_setting('google_api_key', 'AIzaSyBB9ZwnBWmSvxcLLeyz-EEmhG9DBZHP004');

}

    add_action('acf/init', 'my_acf_init');
    /*** Enqueue Google Maps API ***/

function smk_pois_scripts(){

	wp_enqueue_script('Google Maps API', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyBB9ZwnBWmSvxcLLeyz-EEmhG9DBZHP004');

}

add_action('wp_enqueue_scripts', 'smk_pois_scripts');

function TP_map_block($center_map) {

	$pois = new WP_Query(array(

		'post_type'	=> 'poi',

		'posts_per_page'	=> -1,

		'post_status' => 'publish',

		'order' => 'DESC'

	));

	?>

	<div id="map" style="height: 480px;"></div>

	<script type="text/javascript">

		var GoogleMaps;

		function set_map(){

		    GoogleMaps = {

		        $html: jQuery("html, body"),

		        $map: jQuery('#map'),

		        markers: [],

		        init: function () {

		            if (this.$map.length) {

		                this.initMap();

		            }

		        },

		        initMap: function () {

		            this.map = new google.maps.Map(this.$map[0], {

		                zoom: 14,

		                center: new google.maps.LatLng(<?php echo $center_map['lat'] ?>, <?php echo $center_map['lng'] ?>),

		                disableDefaultUI: true,

		                scrollwheel: false,

		                zoomControl: true,

		                zoomControlOptions: {

				            position: google.maps.ControlPosition.LEFT_BOTTOM

				        }

		            });

		        }

		    };

		    GoogleMaps.init();

		}

		function add_marker(lat, lng, iw_title, description){

		    var mapnewmarker = new google.maps.LatLng(lat, lng);

		    var mapnewmarkers = new google.maps.Marker({

		        position: mapnewmarker,

		        title: iw_title,

		        draggable: false,

		        map: GoogleMaps.map

		    });

		    GoogleMaps.markers.push(mapnewmarkers);
		    var infowindow = new google.maps.InfoWindow({
				content:	'<div id="content">'+
      							'<div id="siteNotice">'+
      							'</div>'+
      							'<h5 id="firstHeading" class="firstHeading">'+iw_title+'</h5>'+
								'<p>' +description+ '</p>' +
      						'</div>'
		    });

		    //infowindowlist.push(infowindow);

		    google.maps.event.addListener(mapnewmarkers, 'click', function() {

		        infowindow.open(map,mapnewmarkers);

		   });

		}

		set_map();

	</script>

	<?php
	if ( $pois->have_posts() ) {

		foreach ($pois->posts as $key => $poi) {

			$poi_location = get_field("location", $poi->ID);

			$poi_title = get_the_title($poi->ID);

			$poi_description = get_field("description", $poi->ID);

			?>

			<script type="text/javascript">

				add_marker(

					<?php echo $poi_location['lat']; ?>,

					<?php echo $poi_location['lng']; ?>,

					"<?php echo $poi_title; ?>",

					"<?php echo $poi_description; ?>",


				)

			</script>

			<?php

		}

	}

}
