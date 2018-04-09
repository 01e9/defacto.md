import 'bootstrap';
import { initSlugGenerator } from "./slug-generator";
import "./editors";

jQuery(($) => {
    $('input[data-slug-from]').each((i, slugInput) => {
        const $slug = $(slugInput);

        if ($slug.val().length) {
            return; // skip already existing value
        }

        const $sources = $(
            $slug.attr('data-slug-from')
                .split(',')
                .map(nameSelector => 'input[name="' + $.trim(nameSelector) + '"]')
                .join(',')
        );

        initSlugGenerator($sources, $slug);
    });
});