import { executeScript, includeScript } from "./utils";
import { JSDOM } from "jsdom";

it("executeScript", () => {
    const jsdom = new JSDOM('<!doctype html><html><body></body></html>');
    const { document } = jsdom.window;

    expect(document.body.querySelector("script")).toBe(null);

    executeScript("var x=1;", document);

    const script = document.body.querySelector("script");
    expect(script).not.toBe(null);
    expect(script.getAttribute("src")).toBe("data:text/plain;base64,dmFyIHg9MTs=");
    expect(script.onerror).toBe(script.onload);

    script.onload.apply(script);
    expect(document.body.querySelector("script")).toBe(null);
});

describe("includeScript", () => {
    const src = "https://test.js";

    it("multiple promises onload then others instantly", async () => {
        const jsdom = new JSDOM('<!doctype html><html><body></body></html>');
        const { document } = jsdom.window;

        expect(document.body.querySelector("script")).toBe(null);

        let promise1Resolved = false;
        const promise1 = includeScript(src, document).then(() => { promise1Resolved = true });
        const promise2 = includeScript(src, document);

        const scripts = document.body.querySelectorAll("script");
        expect(scripts).toHaveLength(1);

        scripts[0].onload.apply(scripts[0]);

        expect(promise1Resolved).toBe(false);
        await promise1;
        expect(promise1Resolved).toBe(true);

        await expect(promise2).resolves.toBe(undefined);

        await expect(includeScript(src, document)).resolves.toBe(undefined);
        await expect(includeScript(src, document)).resolves.toBe(undefined);
    });
    it("multiple promises onerror then others instantly", async () => {
        const jsdom = new JSDOM('<!doctype html><html><body></body></html>');
        const { document } = jsdom.window;

        expect(document.body.querySelector("script")).toBe(null);

        let promise1Rejected = false;
        const promise1 = includeScript(src, document).catch(() => promise1Rejected = true);
        const promise2 = includeScript(src, document);

        const scripts = document.body.querySelectorAll("script");
        expect(scripts).toHaveLength(1);

        scripts[0].onerror.apply(scripts[0]);

        expect(promise1Rejected).toBe(false);
        await promise1;
        expect(promise1Rejected).toBe(true);

        await expect(promise2).rejects.toBe(undefined);

        await expect(includeScript(src, document)).rejects.toBe(undefined);
        await expect(includeScript(src, document)).rejects.toBe(undefined);
    });
});
