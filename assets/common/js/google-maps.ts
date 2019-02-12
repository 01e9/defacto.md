import { includeScript } from "./utils";

export function includeGoogleMapsScript(document: Document) {
    const meta = document.querySelector("head > meta[name='google-maps-api-key']");
    const apiKey = meta ? meta.getAttribute("content") : "";
    return includeScript("https://maps.googleapis.com/maps/api/js?key=" + encodeURIComponent(apiKey), document);
}