import {includeScript} from "../../common/js/utils";

/* global google */

let scriptIncluded = false;

function init(element) {
    if (typeof google === "undefined") {
        return;
    }

    $(element).find('.google-map-widget:not(.initialized)').addClass('initialized').each((i, wrapper) => {
        const elements = {
            map: $(wrapper).find(".map").get(0),
            $lat: $(wrapper).find("input.lat"),
            $lng: $(wrapper).find("input.lng")
        };

        const center = {
            lat: parseFloat(elements.$lat.val()) || 47.025528,
            lng: parseFloat(elements.$lng.val()) || 28.830481
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
    if (scriptIncluded) {
        init(element);
    } else {
        scriptIncluded = true;

        const apiKey = $("head > meta[name='google-maps-api-key']").attr("content");
        includeScript("https://maps.googleapis.com/maps/api/js?key=" + apiKey)
            .then(() => init(element));
    }
}
