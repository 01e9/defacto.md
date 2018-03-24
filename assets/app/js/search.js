import InstantSearch from 'instantsearch.js/es/lib/InstantSearch'
import Hits from 'instantsearch.js/es/widgets/hits/hits'
import SearchBox from 'instantsearch.js/es/widgets/search-box/search-box'
import { IS_PRODUCTION, ALGOLIA_APP_ID, ALGOLIA_API_KEY, LANG } from "../../config";

jQuery(function ($) {
    let $modal = $('#instant-search-modal'),
        $input = $modal.find('.modal-header input[type="text"]'),
        $results = $modal.find('.modal-body');

    if (!$modal.length) {
        return console.log('Skipped search modal init');
    }

    $modal
        .on('shown.bs.modal', () => $input.focus())
        .one('show.bs.modal', () => init($input, $results));
});

function init($input, $results) {
    let search = new InstantSearch({
        appId: ALGOLIA_APP_ID,
        apiKey: ALGOLIA_API_KEY,
        indexName: (IS_PRODUCTION ? 'prod_' : 'dev_') + 'promises',
        searchParameters: {
            hitsPerPage: 10
        }
    });

    search.addWidget(
        SearchBox({
            container: $input.get(0),
            autofocus: false,
            magnifier: false,
            reset: false,
            wrapInput: false
        })
    );

    search.addWidget(
        Hits({
            container: $results.get(0),
            templates: {
                item: '<a href="/'+ LANG +'/p/{{slug}}">{{{_highlightResult.name.value}}}</a>',
                empty: '<span class="fa fa-minus-circle"></span>'
            },
            cssClasses: {
                empty: 'text-muted pt-3',
                root: 'list-group',
                item: 'list-group-item px-0'
            }
        })
    );

    search.start();
}