import ClassicEditor from "@ckeditor/ckeditor5-build-classic/build/ckeditor"
import "@ckeditor/ckeditor5-build-classic/build/translations/ro"

document.body.addEventListener("app:initElement", (e) => {
    $(e.detail).find('textarea.wysiwyg').each((i, textarea) => {
        ClassicEditor.create(textarea, {
            toolbar: ['bold', 'italic', 'blockQuote', 'link', 'bulletedList', 'numberedList'],
            language: 'ro'
        });
    });
});
