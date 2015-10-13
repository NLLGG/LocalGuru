<?php
	require_once dirname(__DIR__).'/lib/init.php';
?>

/***************************************************************************

Javascript for dynamic and interactive map Buurtlinux-helpers on OSM.
Copyright (C) 2013	Ruud Beukema

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

***************************************************************************/


/* Set defaults of this map */
var g_countrycode = 'nl';
var g_address = 'Hilversum';
var g_radius = 250000;
var g_center_lonlat;
var g_popup_active = null;
var g_map = null;

// Takes care of rendering the Buurtlinux map with the right center, radius, zoom-level
// and with Linux-helpers as markers
function load_map() {
	// Load default map with base-layer and default controls
	g_map = new OpenLayers.Map("map_canvas", { controls: [
		new OpenLayers.Control.Navigation() ]	} );
	g_map.addLayer( new OpenLayers.Layer.OSM() );
	
	geocode_focus_point();	
	load_buurtlinux_layer();	
	focus_map();
}


// Draw selected linux-helpers to the OpenLayers-canvas
function load_buurtlinux_layer() {
	// Load Linux-helpers from file as layer
	var l_helpers = new OpenLayers.Layer.Markers("Linux-helpers");
	g_map.addLayer(l_helpers);
	
	var l_size = new OpenLayers.Size(48,48);
	var l_offset = new OpenLayers.Pixel(-(l_size.w/2), -l_size.h);
	<?php
	// Print location of Tux-image to javascript file.
	print "var l_icon = new OpenLayers.Icon('".MAP_URL."img/tux.png', l_size, l_offset);";
	?>

	<?php
		// Obtain the linux-helpers that were selected based on the search 
		// criteria.
		require_once('../lib/linux_helpers.php');
		$l_helpers = new LinuxHelpers();
		$l_helpers->process_search_criteria( $search['distros'], $search['desktops'], $search['actions'], $search['groups'], $search['targets'], $search['rewards'] );
		$l_selection = $l_helpers->select_helpers();
		$l_first_marker = true;
	
		// Display only selected users
		if( count($l_selection) )
		{
			foreach( $l_selection as $helper )
			{
				// From the available information extract only what we need
				$l_fullname = $l_helpers->get_full_name( $helper );
				$l_address 	= $l_helpers->get_address( $helper );
				$l_country 	= $l_helpers->get_country_code( $helper );
				$l_summary 	= $l_helpers->get_balloon_text( $helper );
				$l_loc_lat	= $helper['loc_lat'];
				$l_loc_lon	= $helper['loc_lon'];
				
				// Let javascript transform coordinates into right projection-coordinates
				print "var l_lonlat = new OpenLayers.LonLat(".$l_loc_lon.",".$l_loc_lat.").transform(new OpenLayers.Projection('EPSG:4326'), new OpenLayers.Projection('EPSG:900913') );	// transform from WGS 1984 to Spherical Mercator Projection\n";				
				
				// Only load the Tux-icon once, after that clone it!
				if( $l_first_marker )
				{
					$l_first_marker = false;					
					print "var l_marker = new OpenLayers.Marker(l_lonlat, l_icon);\n
						l_marker.events.register('mousedown', l_marker, 
							function(evt) {
								var l_summary = '".$l_summary."';\n
								show_popup( l_summary, l_icon, this.lonlat);
								OpenLayers.Event.stop(evt);
							}
						);\n";
				}
				else
				{
					print "var l_marker = new OpenLayers.Marker(l_lonlat, l_icon.clone());\n
					l_marker.events.register('mousedown', l_marker,
						function(evt) {													
							var l_summary = '".$l_summary."';\n
							show_popup( l_summary, l_icon.clone(), this.lonlat);
							OpenLayers.Event.stop(evt);
						}
					);\n";
				}
				
				print "\tl_helpers.addMarker( l_marker );\n\n";
			}
		}
	?>
}


// Opens the given url [a_url] in a popup browser-window with the given title
// [a_title], width [a_width] and height [a_height].
function open_new_window( a_title, a_url, a_width, a_height )
{
	var l_left = (screen.width/2)-(a_width/2);
	var l_top = (screen.height/2)-(a_height/2);
	var targetWin = window.open (a_url, a_title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, copyhistory=no, width='+a_width+', height='+a_height+', top='+l_top+', left='+l_left);
}


// Hides the currently visible popup-balloon
function hide_popup() {
	g_map.removePopup( g_popup_active );
	g_popup_active = null;
}


// Shows a single popup-balloon
function show_popup( a_content, a_anchor, a_lonlat ) {	
	var l_new_popup = new OpenLayers.Popup.Anchored(
		null,			// no ID
		a_lonlat,		// coordinates of popup
		null,			// no size (autosize)
		a_content,	// HTML content
		a_anchor,	// attach this popup to a Tux icon
		false,		// don't display a close box in the popup
		hide_popup	// close box callback for administration
	);

	l_new_popup.autoSize = true;
	l_new_popup.keepInMap = true;
	l_new_popup.setOpacity( 0.95 );
	
	// Is there a previous popup shown?
	if( g_popup_active != null ) {
		// Check if any current balloon from same marker and
		// if so hide the balloon
		if( g_popup_active.lonlat == l_new_popup.lonlat ) {
			g_map.removePopup( g_popup_active );
			g_popup_active = null;
		}
		else {
			// Hide any other marker's popped up balloon
			if( g_popup_active != null ) {		
				g_map.removePopup( g_popup_active );
			}
			
			// Add this new balloon
			g_popup_active = l_new_popup;
			g_map.addPopup( g_popup_active );	
		}
	} else {
		// Add this new balloon
		g_popup_active = l_new_popup;
		g_map.addPopup( g_popup_active );
	}
}


// Translates the current values for address (g_address), radius (g_radius) and 
// countrycode (g_countrycode) into the corresponding focus-point for the map
// (as g_center_lonlat).
function geocode_focus_point() {
<?php	
	// Obtain address and radius parameters if given. If not use the javascript
	// defaults.
	if( isset( $search['address'] ) && $search['address'] != "" )
	{
		print "g_address = '".$search['address']."';\n";
	}
	if( isset( $search['country'] ) && $search['country'] != "" )
	{
		print "g_countrycode = '".$search['country']."';\n";
	}
?>
	var key = "<?php print OPENCAGE_KEY; ?>";
	// URL that queries the Nominatim search engine for nodes, ways, relations etc.
	// Limit the results to 1 at maximum
	var l_nominatim_url = 'https://api.opencagedata.com/geocode/v1/xml?q='+g_address+'&countrycode='+g_countrycode+'&limit=1&no_annotations=1&key='+key;
	
	// Create an object that can load external XML pages/files
	l_req = new XMLHttpRequest();
	
	// What to do if server has responded to our request
	l_req.onreadystatechange = function() { // instructions to process the response
		if( l_req.readyState  == 4 )
		{
			if( l_req.status  == 200 ) {
				var xmlDoc = l_req.responseXML;
				var geometry = xmlDoc.getElementsByTagName('geometry')[0];

				// Did the query result in places?
				if( geometry.hasChildNodes ) {
					var lat = geometry.firstChild.childNodes[0].nodeValue;
					var lon = geometry.lastChild.childNodes[0].nodeValue;
					g_center_lonlat = new OpenLayers.LonLat(lon, lat);
				} else {
					alert('Het opgegeven adres kan niet worden gevonden.');
				}
			} else {
				alert('Fout tijdens geocoderen van opgegeven adres. Error code: ' + l_req.status);
			}
		}
	}; 
	
	// Send our request synchonously
	try {
		l_req.open("GET", l_nominatim_url, false );
		l_req.send(); 
	}
	catch(error) {
		alert("Er is een onverwacht probleem opgetreden tijdens het geocoderen van het adres.\n\nEen overzichtskaart van Nederland zal worden weergegeven.\n\nError code:\n\n" + error.message);
		// Center on Netherlands
		g_center_lonlat = new OpenLayers.LonLat( 5.169499, 52.22856 );
	}
}


// Translates current focus-point (g_center_lonlat) and radius (g_radius) into
// the corresponding display of the OSM.
function focus_map() {
	// Transform lon/lat data into x/y data (in meters)
	var l_center = new OpenLayers.LonLat( g_center_lonlat.lon, g_center_lonlat.lat);
	var l_topleft = new OpenLayers.LonLat( g_center_lonlat.lon, g_center_lonlat.lat);
	var l_botright = new OpenLayers.LonLat( g_center_lonlat.lon, g_center_lonlat.lat);
	
	l_center.transform(					// Center of the map
		new OpenLayers.Projection("EPSG:4326"),		// transform degrees
		new OpenLayers.Projection("EPSG:900913") 	// to meters (which is the map's default
	);
	
	l_topleft.transform(					// Top-left point of boundary box
		new OpenLayers.Projection("EPSG:4326"),		// transform degrees
		new OpenLayers.Projection("EPSG:900913") 	// to meters (which is the map's default
	);
	
	l_botright.transform(					// Bottom-right point of boundary box
		new OpenLayers.Projection("EPSG:4326"),		// transform degrees
		new OpenLayers.Projection("EPSG:900913") 	// to meters (which is the map's default
	);
	
	// Calculate actual corners of boundary box in unit 'm'
	l_topleft.lon -=  parseInt(g_radius);		// x
	l_topleft.lat +=  parseInt(g_radius);		// y	
	l_botright.lon  += parseInt(g_radius);		// x	
	l_botright.lat -= parseInt(g_radius);		// y
	
	// Create the boundary box that contains these three points l_center, 
	// l_topleft and l_botright.
	var l_bounds = new OpenLayers.Bounds();
	l_bounds.extend( l_center );
	l_bounds.extend( l_topleft );
	l_bounds.extend( l_botright );	
	l_bounds.toBBOX();
	
	// Zoom map to calculated boundary box
	g_map.zoomToExtent( l_bounds, true ); 

	// Center and bound map
	if (!g_map.getCenter()) g_map.setCenter( g_center_lonlat.transform(	// Center of the map
		new OpenLayers.Projection("EPSG:4326"),				// transform from WGS 1984
		new OpenLayers.Projection("EPSG:900913") 			// to Spherical Mercator Projection
	) );
}
