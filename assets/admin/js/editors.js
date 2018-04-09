import ClassicEditor from "@ckeditor/ckeditor5-build-classic"
import "@ckeditor/ckeditor5-build-classic/build/translations/ro"

jQuery($ => {
    $('textarea.wysiwyg').each((i, textarea) => {
        ClassicEditor.create(textarea, {
            toolbar: [ 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ],
        });
    });
})