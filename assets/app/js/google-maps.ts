import { includeGoogleMapsScript } from "../../common/js/google-maps";

export interface IMapMarkerData {
    lat: string;
    lng: string;
    label: string;
    description: string;
    url: string;
}

export const initGoogleMapWithMarkers: (
    element: Element,
    document: Document,
    markers: IMapMarkerData[]
) => Promise<{
    map: google.maps.Map,
    bounds: google.maps.LatLngBounds
}> = (element, document, markers) => new Promise((resolve, reject) => {
    includeGoogleMapsScript(document).then(() => {
        const map = new google.maps.Map(element, {
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

        map.fitBounds(bounds);

        resolve({map, bounds});
    }).catch(reject);
});
