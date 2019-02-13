import { IMapMarkerData, initGoogleMapWithMarkers } from "./google-maps";
import createGoogleMapsMock from '@wapps/jest-google-maps-mock';
import { JSDOM } from "jsdom";

jest.mock("../../common/js/markerclusterer");

beforeEach(() => {
    const googleMapsMock = createGoogleMapsMock();
    googleMapsMock.Map.prototype.fitBounds = jest.fn(); // https://github.com/hupe1980/wapps-components/pull/19
    googleMapsMock.LatLngBounds.prototype.extend = jest.fn();
    googleMapsMock.Marker.prototype.getPosition = jest.fn();
    // @ts-ignore
    global.google = {maps: googleMapsMock};
});
afterEach(() => {
    // @ts-ignore
    global.google = undefined;
})

it("initGoogleMapWithMarkers", async () => {
    const jsdom = new JSDOM('<!doctype html><html><body></body></html>');
    const { document } = jsdom.window;
    const element = document.createElement("div");
    const markers: IMapMarkerData[] = [{
        lat: "12.3",
        lng: "45.6",
        label: "Label",
        description: "Description",
        url: "https://u.rl"
    }];
    const promise = initGoogleMapWithMarkers(element, document, markers);

    const script = document.querySelector("script");
    expect(script).not.toBeFalsy();
    script.onload.apply(script);

    await expect(promise).resolves.toMatchObject({
        map: expect.any(Object),
        bounds: expect.any(Object)
    });
    expect((global as any).google.maps.Map).toHaveBeenCalledTimes(1);
    expect((global as any).google.maps.Marker).toHaveBeenCalledTimes(1);
});