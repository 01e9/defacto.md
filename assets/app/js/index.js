import 'bootstrap-material-design'
import './search'
import './sliders'
import { includeGoogleMapsScript } from "../../common/js/google-maps";

jQuery($ => {
    $('[data-toggle="tooltip"]').tooltip()
});

jQuery($ => {
    const $maps = $(".map-markers");
    if (!$maps.length) {
        return;
    }

    includeGoogleMapsScript().then(() => { /* global google */
        $maps.each((i, el) => {
            const $map = $(el);
            const markers = $map.data("map-markers");

            const map = new google.maps.Map(el, {
                zoom: 7,
                center: {lat: 47.025528, lng: 28.830481},
                mapTypeControlOptions: {mapTypeIds: []},
                streetViewControl: false
            });

            markers.forEach(markerData => {
                const marker = new google.maps.Marker({
                    map,
                    position: {
                        lat: parseFloat(markerData.lat),
                        lng: parseFloat(markerData.lng)
                    },
                    label: String(markerData.label),
                    title: markerData.description,
                });
                marker.addListener('click', e => {
                    document.location.assign(markerData.url);
                });
            })
        });
    }).catch(() => alert("Map error"));
});