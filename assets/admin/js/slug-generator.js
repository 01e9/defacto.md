// https://gist.github.com/mathewbyrne/1280286
export function slugify(text) {
    return text.toString().toLowerCase()
        .replace(/\s+/g, '-')           // Replace spaces with -
        .replace(/[^ăîșțâ\w\-]+/gi, '')       // Remove all non-word chars
        .replace(/\-\-+/g, '-')         // Replace multiple - with single -
        .replace(/^-+/, '')             // Trim - from start of text
        .replace(/-+$/, '');            // Trim - from end of text
}

export function initSlugGenerator($sources, $slug) {
    const eventNamespace = '.auto-slug';

    $sources.off(eventNamespace);
    $slug.off(eventNamespace);

    $sources.on(['keyup', 'change'].map(event => event + eventNamespace).join(' '), () => {
        let values = [];
        $sources.each((i, sourceInput) => {
            values.push($(sourceInput).val());
        });
        $slug.val(slugify(values.join(' ')));
    });

    $slug.one('change'+ eventNamespace, () => {
        $sources.off(eventNamespace);
    });
}
