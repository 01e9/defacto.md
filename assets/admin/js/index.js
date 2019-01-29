import 'bootstrap';
import { initSlugGenerator } from "./slug-generator";
import "./editors";
import "./datepicker"
import "./log-diff"

document.body.addEventListener("app:initElement", (e) => {
    $(e.detail).find('input[data-slug-from]').each((i, slugInput) => {
        const $slug = $(slugInput);

        const $sources = $(
            $slug.attr('data-slug-from')
                .split(',')
                .map(nameSelector => 'input[name="' + $.trim(nameSelector) + '"]')
                .join(',')
        );

        initSlugGenerator($sources, $slug);
    });
});

jQuery(() => {
    document.body.dispatchEvent(
        new CustomEvent('app:initElement', {detail: document.body})
    );
});