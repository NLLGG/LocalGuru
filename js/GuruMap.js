/***************************************************************************

Javascript that creates the LocalGuru map
Copyright (C) 2013	Ruud Beukema
Copyright (C) 2015	Rutger van Sleen

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

var layer = new ol.layer.Tile({
	source: new ol.source.OSM()
});
var vector = new ol.layer.Vector({
	source: new ol.source.KML({
		projection: 'EPSG:3857',
		url: '/localguru/lib/kml.php'
	})
});
var map = new ol.Map({
	layers: [layer, vector],
	target:'map',
	view: new ol.View({
		center: ol.proj.transform([5.08, 52.12], 'EPSG:4326', 'EPSG:3857'),
		zoom: 7 
	}),
	controls: ol.control.defaults({
		attributionOptions: {
			collapsible: false
		}
	})
});

// Show tooltips when hovering over a Tux
var info = $('#info');
info.tooltip({
	animation: false,
	trigger: 'manual'
});

var displayFeatureInfo = function(pixel) {
	info.css({
		left: pixel[0] + 'px',
		top: (pixel[1] - 15) + 'px'
	});
	var feature = map.forEachFeatureAtPixel(pixel, function(feature, layer) {
		return feature;
	});
	if (feature) {
		info.tooltip('hide')
			.attr('data-original-title', feature.get('name'))
			.tooltip('fixTitle')
			.tooltip('show');
	} else {
		info.tooltip('hide');
	}
};

$(map.getViewport()).on('mousemove', function(evt) {
	displayFeatureInfo(map.getEventPixel(evt.originalEvent));
});
