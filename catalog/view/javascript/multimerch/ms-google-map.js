// necessary variables
var map;
var infoWindow;
var markers = [];
var markersData = [];

$(function(){
	//A Google Maps JavaScript API v3 library to create and manage per-zoom-level clusters for large amounts of markers
	$.getScript('catalog/view/javascript/multimerch/googlemap/markerclusterer/markerclusterer.js');

	// markerSpider for Google Maps - library for display several markers in one position
	$.getScript('catalog/view/javascript/multimerch/googlemap/oms.min.js');

	//A Google Maps JavaScript API v3 library
	$.getScript('https://maps.google.com/maps/api/js?sensor=false&callback=mapInit&key='+$.trim(msGlobals.google_api_key));
});

function mapInit(){
	//set google map with default values
	map = new google.maps.Map(document.getElementById('map_canvas'), {
		zoom: 2,
		center:  {lat: 0, lng: 0}
	});

	// get sellers info for markers
	$.ajax({
		url: 'index.php?route=seller/catalog/jxGetMapSellers',
		dataType: 'json',
		type: 'post',
		success: function (jsonData) {
			if(jsonData['sellers']) {

				// markersData variable stores the information necessary to each marker
				$.map(jsonData['sellers'], function(seller, i) {
						if (seller.position.length > 1) {
							var seller_position = JSON.parse(seller.position);

							//google info window content
							var contentString = '<div class="panel panel-default mm-one-seller"><div class="panel-heading" style="height: 60px;"></div><div class="panel-body text-center">' +
								'<a href="' + seller.href + '">' +
								'<img class="panel-profile-img" src="' + seller.thumb + '"></a>' +
								'<h5 class="panel-title"><a href="' + seller.href + '">' + seller.nickname + '</a></h5>';
							contentString += '<ul class="list-unstyled"><li class="seller-country">' + seller.address + '</li>';
							if (seller.website){
								contentString += '<li class="seller-website"><a target="_blank" href="' + seller.website + '">' + seller.website + '</a></li>';
							}
							contentString += '<li class="seller-total-products"><a href="' + seller.products_href + '">' + seller.total_products + ' products</a></li></ul></div></div>';

							//set marker data
							var marker = {
								lat: seller_position.lat,
								lng: seller_position.lng,
								name: seller.nickname,
								infowindow_content: contentString
							};
							markersData.push(marker);
						}
				});

				// a new Info Window is created
				infoWindow = new google.maps.InfoWindow();

				// event that closes the Info Window with a click on the map
				google.maps.event.addListener(map, 'click', function() {
					infoWindow.close();
				});

				// event that set new styles for Info Window
				google.maps.event.addListener(infoWindow, 'domready', function() {
					var iwOuter = $('.gm-style-iw');
					var iwBackground = iwOuter.prev();
					iwBackground.children(':nth-child(2)').css({'display' : 'none'});
					iwBackground.children(':nth-child(4)').css({'display' : 'none'});

					var iwCloseBtn = iwOuter.next();
					iwCloseBtn.css({
						opacity: '1',
						right: '42px',
						top: '27px',
						cursor: 'pointer'
					});

					iwCloseBtn.addClass('gm-close-button');
					$('.gm-close-button').click(function(){
						infowindow.close();
					})
				});

				// begin the markers creation
				displayMarkers();

				// set MarkerClusterer library with params
				var markerCluster = new MarkerClusterer(map, markers, {
					imagePath: 'catalog/view/javascript/multimerch/googlemap/markerclusterer/images/m',
					gridSize: 30,
					maxZoom: 4
				});
			}
		},
		error: function (error) {
			console.error(error);
		}
	});
}

// Iterate over markersData array
// creating markers with createMarker function
function displayMarkers(){

	// set the map bounds according to markers position
	var bounds = new google.maps.LatLngBounds();

	// get Spiderfier to work with Google Maps
	var oms = new OverlappingMarkerSpiderfier(map);

	// for loop traverses markersData array calling createMarker function for each marker
	for (var i = 0; i < markersData.length; i++){

		var latlng = new google.maps.LatLng(markersData[i].lat, markersData[i].lng);

		// Spiderfier alternative - change position
		// var a = 360.0 / markersData.length;
		// var newLat = markersData[i].lat + -1 * Math.cos((+a*i) / 180 * Math.PI);  // x
		// var newLng = markersData[i].lng + -1 * Math.sin((+a*i) / 180 * Math.PI);  // Y
		// var latlng = new google.maps.LatLng(newLat,newLng);

		marker = createMarker(latlng, markersData[i]);

		// add marker to Spiderfier
		oms.addMarker(marker);

		// marker position is added to bounds variable
		bounds.extend(latlng);
	}

	// Finally the bounds variable is used to set the map bounds
	map.fitBounds(bounds);
}

// Creates each marker and it sets their Info Window content
function createMarker(latlng, data){
	var marker = new google.maps.Marker({
		map: map,
		position: latlng,
		title: data.name
	});

	markers.push(marker);

	// This event expects a click on a marker
	// When this event is fired the Info Window content is created
	// and the Info Window is opened.
	google.maps.event.addListener(marker, 'click', function() {

		// including content to the Info Window.
		infoWindow.setContent(data.infowindow_content);

		// opening the Info Window in the current map and at the current marker location.
		infoWindow.open(map, marker);
	});

	return marker;
}