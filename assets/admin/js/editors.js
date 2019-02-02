import ClassicEditor from "@ckeditor/ckeditor5-build-classic/build/ckeditor"
import "@ckeditor/ckeditor5-build-classic/build/translations/ro"

export function initElementEditors(element) {
    $(element).find('textarea.wysiwyg:not(.initialized)').addClass('initialized').each((i, textarea) => {
        ClassicEditor.create(textarea, {
            toolbar: ['bold', 'italic', 'blockQuote', 'link', 'bulletedList', 'numberedList'],
            language: 'ro'
        });
    });
}
