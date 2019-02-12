import { includeGoogleMapsScript } from "./google-maps";
import { JSDOM } from "jsdom";

describe("includeGoogleMapsScript", () => {
    it("works without API key meta", async () => {
        const jsdom = new JSDOM('<!doctype html><html><body></body></html>');
        const { document } = jsdom.window;

        const promise = includeGoogleMapsScript(document);

        const script = document.querySelector("script");
        expect(script).not.toBeFalsy();
        script.onload.apply(script);
        await expect(promise).resolves.toBe(undefined);
    });
    it("with API key meta", async () => {
        const jsdom = new JSDOM(
            '<!doctype html><html><head><meta name="google-maps-api-key" content="test" /></head><body></body></html>'
        );
        const { document } = jsdom.window;

        const promise = includeGoogleMapsScript(document);

        const script = document.querySelector("script");
        expect(script).not.toBeFalsy();
        script.onload.apply(script);
        await expect(promise).resolves.toBe(undefined);

        expect(script.getAttribute("src").indexOf("?key=test")).not.toBe(-1);
    });
});
