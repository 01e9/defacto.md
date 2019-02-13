import { includeScript } from "./utils";

export function includeGoogleMapsScript(document: Document) {
    const apiKeyMeta = document.querySelector("head > meta[name='google-maps-api-key']");
    const apiKey = apiKeyMeta ? apiKeyMeta.getAttribute("content") : "";

    const lang = document.querySelector('html').getAttribute('lang');

    return includeScript(
        "https://maps.googleapis.com/maps/api/js?key=" + encodeURIComponent(apiKey) + "&language=" + lang,
        document
    );
}