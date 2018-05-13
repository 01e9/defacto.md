import 'bootstrap';
import { initSlugGenerator } from "./slug-generator";
import "./editors";
import "./datepicker"

jQuery($ => {
    $('input[data-slug-from]').each((i, slugInput) => {
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