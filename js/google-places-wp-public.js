/**
 * Google Places WP Public JS
 * version: 1.0
 * @param $ - jQuery
 */

import '../css/google-places-wp-public.css';

const googlePlacesWP = async ($) => {
	
	if(typeof google_places_wp === "undefined") {
		console.warn("You need to configure your map settings in the Google WP Places admin settings area.");
		return; 
	} else if(typeof google_places_wp.mapdata.coords.lat === "undefined") {
		console.warn("You need to configure your map settings in the Google WP Places admin settings area.");
		return; 
	}

	if(typeof $ === 'undefined') {
		console.warn("jQuery conflict error! Google Places WP couldn't properly load or find jQuery on the page.");
		return; 
	}

	if(typeof google === 'undefined') {
		console.warn("Error loading Google Maps JS, is your API key set properly?");
		return; 
	}

	let map,infowindow,
		markersPlaced = [],
		savedPlaces = google_places_wp.mapdata.places; 

	$(document).ready(async ($) => { 
		
		const { Map, InfoWindow } 	    = await google.maps.importLibrary("maps");
		const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
		const { LatLngBounds }    		= await google.maps.importLibrary("core");
	
		let targetLocation = { 
			lat: google_places_wp.mapdata.coords.lat, 
			lng: google_places_wp.mapdata.coords.lng
		},
		mapdiv = document.getElementById("google-places-wp-map"),
		z = 13;
		
		map = new Map(mapdiv, {
			center: targetLocation,
			mapId: "DEMO_MAP_ID", //TODO: setup additional field in admin area to allow users to use their own custom map styles.
			zoom: z
		});
		
		infowindow = new InfoWindow({disableAutoPan: true});

		const bounds  = new LatLngBounds(),
			  marker  = new AdvancedMarkerElement({
				map: map,
				position: targetLocation, 
				zIndex: 4,
			});
		
		google.maps.event.addListener(marker, 'click', () => { 
			let info_html = `<div class="google-places-wp-info-window"><span class="google-places-wp-iw-title">${google_places_wp.mapdata.location_name}</span><br/><br><span>${google_places_wp.mapdata.street_address}<br>${google_places_wp.mapdata.city}</span><br><a href="${google_places_wp.mapdata.map_url}" target="_blank">Get Directions &raquo;</a></div>`;

			infowindow.setContent(info_html);
			infowindow.open(map, this);
		});

		$('.map-controller button').map((idx,button) => { 
			let placeType = $(button).data("place-type"),
			placeTypePlaces = [];
			
			for(const placeID of Object.keys(google_places_wp.mapdata.places)) { 
				if(savedPlaces[placeID].types == placeType) { 	
					placeTypePlaces.push(savedPlaces[placeID]);
				}
			}
				
			let span = document.createElement('span');
			
			span.innerHTML = ` (${placeTypePlaces.length})`;
			span.className = 'gp-wp-place-type-total';
			
			button.append(span);

		}); 

		function panToWithOffset(map, latlng, offsetX, offsetY) {
			const scale                 = Math.pow(2, map.getZoom());
			const worldCoordinateCenter = map.getProjection().fromLatLngToPoint(latlng);
			const pixelOffset = new google.maps.Point(
			  (offsetX / scale) || 0,
			  (offsetY / scale) || 0
			);
			const worldCoordsNewCenter  = new google.maps.Point(
				worldCoordinateCenter.x + pixelOffset.x,
				worldCoordinateCenter.y - pixelOffset.y
			);
		
			const newCenter = map.getProjection().fromPointToLatLng(worldCoordsNewCenter);
			map.panTo(newCenter);
		  }

		const addMarkerToMap = (placeData) => {
			
			if(!placeData) return; 
	
			let latLng = { 
				lat: parseFloat(placeData.lat), 
				lng: parseFloat(placeData.lng)
				},
			marker = new AdvancedMarkerElement({
				map: map,
				position: latLng,
				title: placeData.name,               
			});
	
			markersPlaced.push(marker);

			google.maps.event.addListener(marker, "click", function() {

				let infoWindowContent = `<div class="google-places-wp-infowindow"><p class="google-places-wp-iw-title">${placeData.name}</p><div class="google-places-wp-iw-photo" style="height: 115px; width: 20vw; max-width: 100%; background: url('${placeData.photo}') top center no-repeat; background-size: cover;"></div><p class="google-places-wp-iw-address">${placeData.pretty_address}</p><div class="google-places-wp-iw-links"><a href="${placeData.url}" target="_blank">View Website</a><br/><a href="https://www.google.com/maps?q=place_id:${placeData.location_id}" target="_blank">Get Directions &raquo;</a></div></div>`;
	
				infowindow.setContent(infoWindowContent);
				infowindow.open(map, this);
				panToWithOffset(map, latLng, 0, 160);
			});
			
			marker.setMap(map);
			return marker;
		}
	
		const addPlaceMarkers = (places, curTarget, bounds) => { 
			
			if(!places.length) return; 
			
			if(markersPlaced.length) { 
				for(let i = 0; i < markersPlaced.length; i++) { 
					markersPlaced[i].setMap(null);
				}
			}
	
			// Reset marker array 
			markersPlaced = [];
			let markerTypeContent = curTarget.next(),
				tags = []; 

			places.map((place) => {
				let marker = addMarkerToMap(place);
				
				bounds.extend({
					lat: parseFloat(place.lat),
					lng: parseFloat(place.lng)
				});

				let placeContentTag = document.createElement('p'),
					placeAnchorTag  = document.createElement('a');

				placeAnchorTag.setAttribute("data-place-id", place.location_id);
				placeAnchorTag.innerHTML = place.name;

				placeContentTag.appendChild(placeAnchorTag);

				placeAnchorTag.addEventListener('click', () => {
					panToWithOffset(map, { lat: parseFloat(place.lat), lng: parseFloat(place.lng) }, 0, -180);
					google.maps.event.trigger(marker, 'click');
				});

				tags.push(placeContentTag);
			});
			
			map.fitBounds(bounds);
			markerTypeContent.append(tags); 
			markerTypeContent.slideDown();
		}
	
		$('.map-controller button').on('click', async (e) => { 
			let thisButton = $(e.currentTarget);
			if (thisButton.hasClass('google-places-wp-is-open')) return; 
			
			$('.map-controller button').removeClass('google-places-wp-is-open');
			
			thisButton.addClass('google-places-wp-is-open');

			let placeType 		= thisButton.data('place-type'),
				placeTypePlaces = []; 
			
			await (new Promise(x => $('.google-wp-places-places-listed').slideUp('fast',x))).then(() => {
				$('.google-wp-places-places-listed').empty();
				
				for(const placeID of Object.keys(google_places_wp.mapdata.places)) { 
					if(savedPlaces[placeID].types == placeType) { 
						placeTypePlaces.push(savedPlaces[placeID]);
					}
				} 
				
				addPlaceMarkers(placeTypePlaces, thisButton, bounds );
			  });
			});
	  });
}

googlePlacesWP(jQuery);

if (import.meta.hot) {
  import.meta.hot.accept();
}

export default { googlePlacesWP };