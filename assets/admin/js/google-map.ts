import { includeGoogleMapsScript } from "../../common/js/google-maps";
import $ from "jquery";

/* global google */

function init(element) {
    $(element).find('.google-map-widget:not(.initialized)').addClass('initialized').each((i, wrapper) => {
        const elements = {
            map: $(wrapper).find(".map").get(0),
            $lat: $(wrapper).find("input.lat"),
            $lng: $(wrapper).find("input.lng")
        };

        const center = {
            lat: parseFloat(elements.$lat.val().toString()) || 47.025528,
            lng: parseFloat(elements.$lng.val().toString()) || 28.830481
        };

        const map = new google.maps.Map(elements.map, {
            zoom: 10,
            center,
            mapTypeControlOptions: {mapTypeIds: []},
            streetViewControl: false
        });

        const marker = new google.maps.Marker({
            map,
            position: center,
            draggable: true,
            animation: google.maps.Animation.DROP
        });
        marker.addListener('dragend', e => {
            elements.$lat.val(e.latLng.lat());
            elements.$lng.val(e.latLng.lng());

            map.setCenter(e.latLng);
        });
    });
}

export function initElementGoogleMaps(element) {
    includeGoogleMapsScript(document).then(() => init(element)).catch(() => alert("Map init error"));
}
