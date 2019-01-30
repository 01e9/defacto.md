export function executeScript(source) {
    const script = document.createElement("script");
    script.onload = script.onerror = function(){ this.remove(); };
    script.src = "data:text/plain;base64," + btoa(source);
    document.body.appendChild(script);
}

export function includeScript(src) {
    return new Promise((resolve, reject) => {
        const script = document.createElement("script");
        script.onload = resolve;
        script.onerror = reject;
        script.src = src;
        document.body.appendChild(script);
    });
}
