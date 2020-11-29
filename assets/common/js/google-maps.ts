import { includeScript, selectMetaContent } from "./utils";
import { LANG, META_NAME_GOOGLE_MAPS_API_KEY } from "~/config";

export function includeGoogleMapsScript(document: Document) {
    const apiKey = selectMetaContent(META_NAME_GOOGLE_MAPS_API_KEY);
    const src = `https://maps.googleapis.com/maps/api/js?key=${encodeURIComponent(apiKey)}&language=${LANG}`;

    return includeScript(src, document);
}