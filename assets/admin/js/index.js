import 'bootstrap';
import 'froala-editor';
import 'froala-editor/js/plugins/image.min';
import 'froala-editor/js/plugins/code_view.min';
import { initSlugGenerator } from "./slug-generator";

jQuery(($) => {
    $('textarea.wysiwyg').froalaEditor();
});

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