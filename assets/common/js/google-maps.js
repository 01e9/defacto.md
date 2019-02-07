import { includeScript } from "./utils";

export function includeGoogleMapsScript() {
    const key = $("head > meta[name='google-maps-api-key']").attr("content");
    return includeScript("https://maps.googleapis.com/maps/api/js?key=" + key, document);
}