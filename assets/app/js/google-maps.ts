import { includeGoogleMapsScript } from "../../common/js/google-maps";
import MarkerClusterer from "../../common/js/markerclusterer";

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
}> = (element, document, markersData) => new Promise((resolve, reject) => {
    includeGoogleMapsScript(document).then(() => {
        const map = new google.maps.Map(element, {
            mapTypeControlOptions: {mapTypeIds: []},
            streetViewControl: false
        });

        const markers = markersData.map(markerData => {
            const marker = new google.maps.Marker({
                map,
                position: new google.maps.LatLng(
                    parseFloat(markerData.lat),
                    parseFloat(markerData.lng)
                ),
                label: String(markerData.label),
                title: markerData.description,
            });
            marker.addListener('click', () => document.location.assign(markerData.url));

            return marker;
        });

        const markerCluster = new MarkerClusterer(map, markers, {imagePath: '/img/markerclusterer/m'});

        const bounds = new google.maps.LatLngBounds();
        markers.forEach(marker => bounds.extend(marker.getPosition()));
        map.fitBounds(bounds);

        resolve({map, bounds});
    }).catch(reject);
});
