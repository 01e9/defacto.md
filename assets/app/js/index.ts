import 'bootstrap'
import './search'
import './sliders'
import * as $ from "jquery";
import { initGoogleMapWithMarkers } from "./google-maps";

$(() => {
    $('[data-toggle="tooltip"]').tooltip();
});

$(() => {
    $(".map-markers").each((i, element) => {
        initGoogleMapWithMarkers(element, document, $(element).data("map-markers") || [])
            .then(({map, bounds}) => {
                $(window).on("resize", () => map.fitBounds(bounds));
            });
    })
});