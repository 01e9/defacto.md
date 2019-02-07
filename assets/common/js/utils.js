export function executeScript(source) {
    const script = document.createElement("script");
    script.onload = script.onerror = function(){ this.remove(); };
    script.src = "data:text/plain;base64," + btoa(source);
    document.body.appendChild(script);
}

function addScriptPromiseCallbacks(script, resolve, reject) {
    script.pendingPromises = script.pendingPromises || [];
    script.pendingPromises.push({resolve, reject});
}

export const includeScript = (src, document) => new Promise((resolve, reject) => {
    let script = document.querySelector('script[src="' + src + '"]');
    if (script) {
        if (typeof script.pendingPromisesStatus === "undefined") {
            addScriptPromiseCallbacks(script, resolve, reject);
        } else {
            script.pendingPromisesStatus ? resolve() : reject();
        }
    } else {
        script = document.createElement("script");
        addScriptPromiseCallbacks(script, resolve, reject);
        script.onload  = () => setTimeout(() => {
            script.pendingPromisesStatus = true;
            script.pendingPromises.map(({resolve}) => resolve());
        }, 0);
        script.onerror = () => {
            script.pendingPromisesStatus = false;
            script.pendingPromises.map(({reject})  => reject());
        };
        script.src = src;
        document.body.appendChild(script);
    }
});
