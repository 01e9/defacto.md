export function executeScript(source: string, document: Document): void {
    const script = document.createElement("script");
    script.onload = script.onerror = function(){ this.remove(); };
    script.src = "data:text/plain;base64," + btoa(source);
    document.body.appendChild(script);
}

export const includeScript: (src: string, document: Document) => Promise<{}> = (() => {
    const pendingPromisesProp = "_appPendingPromises";
    const pendingPromisesStatusProp = "_appPendingPromisesStatus";

    function addScriptPromiseCallbacks(script: HTMLScriptElement, resolve: () => void, reject: () => void) {
        script[pendingPromisesProp] = script[pendingPromisesProp] || [];
        script[pendingPromisesProp].push({resolve, reject});
    }

    return (src: string, document: Document) => new Promise((resolve, reject) => {
        let script = document.querySelector('script[src="' + src + '"]') as HTMLScriptElement;
        if (script) {
            if (typeof script[pendingPromisesStatusProp] === "undefined") {
                addScriptPromiseCallbacks(script, resolve, reject);
            } else {
                setTimeout(() => script[pendingPromisesStatusProp] ? resolve() : reject(), 0);
            }
        } else {
            script = document.createElement("script");
            addScriptPromiseCallbacks(script, resolve, reject);
            script.onload = () => setTimeout(() => {
                script[pendingPromisesStatusProp] = true;
                script[pendingPromisesProp].map(({resolve}) => resolve());
            }, 0);
            script.onerror = () => setTimeout(() => {
                script[pendingPromisesStatusProp] = false;
                script[pendingPromisesProp].map(({reject}) => reject());
            }, 0);
            script.src = src;
            document.body.appendChild(script);
        }
    });
})();

export const selectMetaContent = (metaName: string): string => {
    const meta = document.querySelector(`head > meta[name="${metaName}"]`);
    return meta ? meta.getAttribute("content") : "";
};
