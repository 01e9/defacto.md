import ClassicEditor from "../ckeditor/build/ckeditor";
import { LANG } from "../../config";

export function initElementEditors(element) {
    $(element).find('textarea.wysiwyg:not(.initialized)').addClass('initialized').each((i, textarea) => {
        ClassicEditor.create(textarea, {
            extraPlugins: [ MyCustomUploadAdapterPlugin, ],
            toolbar: [
                'heading',
                '|', 'bold', 'italic', 'blockQuote', 'link', 'bulletedList', 'numberedList', 'imageUpload', 'horizontalLine', 'insertTable',
                '|', 'undo', 'redo'
            ],
            language: 'ro'
        });
    });
}

//region Upload Image Adapter

class MyUploadAdapter {
    url: string = `/${LANG}/admin/editor-images`;
    loader;
    xhr: XMLHttpRequest;

    constructor(loader) {
        this.loader = loader;
    }

    upload() {
        return new Promise((resolve, reject) => {
            this._initRequest();
            this._initListeners(resolve, reject);
            this._sendRequest();
        });
    }

    abort() {
        if (this.xhr) {
            this.xhr.abort();
        }
    }

    _initRequest() {
        const xhr = this.xhr = new XMLHttpRequest();

        xhr.open('POST', this.url, true);
        xhr.responseType = 'json';
    }

    _initListeners(resolve, reject) {
        const xhr = this.xhr;
        const loader = this.loader;
        const genericErrorText = 'Couldn\'t upload file:' + ` ${loader.file.name}.`;

        xhr.addEventListener('error', () => reject(genericErrorText));
        xhr.addEventListener('abort', () => reject());
        xhr.addEventListener('load', () => {
            const response = xhr.response;

            if (!response || response.error) {
                return reject(response && response.error ? response.error.message : genericErrorText);
            }

            resolve({default: response.url});
        });

        if (xhr.upload) {
            xhr.upload.addEventListener('progress', evt => {
                if (evt.lengthComputable) {
                    loader.uploadTotal = evt.total;
                    loader.uploaded = evt.loaded;
                }
            });
        }
    }

    _sendRequest() {
        this.loader.file.then(image => {
            const data = new FormData();

            data.append('image', image);

            this.xhr.send(data);
        });
    }
}

function MyCustomUploadAdapterPlugin(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = loader => {
        return new MyUploadAdapter(loader);
    };
}

//endregion
