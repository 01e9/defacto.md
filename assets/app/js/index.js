import 'bootstrap-material-design'
import './search'
import './sliders'
import { includeGoogleMapsScript } from "../../common/js/google-maps";

jQuery($ => {
    $('[data-toggle="tooltip"]').tooltip()
});

/* global google */

jQuery($ => {
    const $maps = $(".map-markers");
    if (!$maps.length) {
        return;
    }

    includeGoogleMapsScript().then(() => $maps.each((i, el) => {
        const $map = $(el);
        const markers = $map.data("map-markers");

        const map = new google.maps.Map(el, {
            mapTypeControlOptions: {mapTypeIds: []},
            streetViewControl: false
        });
        const bounds = new google.maps.LatLngBounds();

        markers.forEach(markerData => {
            const position = new google.maps.LatLng(
                parseFloat(markerData.lat),
                parseFloat(markerData.lng)
            );
            const marker = new google.maps.Marker({
                map,
                position,
                label: String(markerData.label),
                title: markerData.description,
            });
            marker.addListener('click', () => document.location.assign(markerData.url));

            bounds.extend(position);
        });

        {
            map.fitBounds(bounds);
            $(window).on("resize", () => console.log(map.fitBounds(bounds)));
        }
    })).catch(() => alert("Map error"));
});