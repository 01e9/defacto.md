import { diffLines } from 'diff'

jQuery($ => {
    const $modal = $(
        '<div class="modal fade" tabindex="-1" role="dialog">' +
        '  <div class="modal-dialog modal-lg" role="document">' +
        '    <div class="modal-content">' +
        '      <div class="modal-header">' +
        '        <button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
        '          <span aria-hidden="true">&times;</span>' +
        '        </button>' +
        '      </div>' +
        '      <div class="modal-body">' +
        '          <pre></pre>' +
        '      </div>' +
        '    </div>' +
        '  </div>' +
        '</div>'
    );

    function generateDiff(before, after) {
        const fragment = document.createDocumentFragment();

        diffLines(before, after).forEach(function(part){
            let span = document.createElement('span');
            span.className = part.added
                ? 'text-success font-weight-bold'
                : part.removed
                    ? 'text-danger font-weight-bold'
                    : 'text-muted';
            span.appendChild(document.createTextNode(part.value));
            fragment.appendChild(span);
        });

        return fragment;
    }

    $(document.body).on('click', 'a.app-log-diff', (event) => {
        const $data = $(event.target);

        $modal.find('pre').html('').append(generateDiff($data.data('before'), $data.data('after')));

        $(document.body).append($modal);

        $modal.modal('show');
    });
});