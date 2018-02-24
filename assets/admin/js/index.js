import 'froala-editor';
import 'froala-editor/js/plugins/image.min';
import 'froala-editor/js/plugins/code_view.min';

jQuery(($) => {
    $('textarea.wysiwyg').froalaEditor();
})