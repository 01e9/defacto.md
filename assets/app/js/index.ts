import 'bootstrap';
import './search';
import './sliders';
import $ from "jquery";
import { initGoogleMapWithMarkers } from "./google-maps";
import "bootstrap-select/js/bootstrap-select";

$(() => $('[data-toggle="tooltip"]').tooltip());
$(() => $('[data-toggle="popover"]').popover());

$(() => $(".map-markers").each((i, element) => {
    !initGoogleMapWithMarkers(element, document, $(element).data("map-markers") || []);
}));
