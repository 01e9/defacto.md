import InstantSearch from 'instantsearch.js/es/lib/InstantSearch'
import Hits from 'instantsearch.js/es/widgets/hits/hits'
import SearchBox from 'instantsearch.js/es/widgets/search-box/search-box'
import { IS_PRODUCTION, ALGOLIA_APP_ID, ALGOLIA_API_KEY } from "../../config";

jQuery(function ($) {
    let $form = $('.instant-search-form').first();

    if (!$form.length) {
        return;
    }

    let $input = $form.find('input[type="text"]'),
        $result = $form.find('.instant-search-results');

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
            wrapInput: false,
            queryHook(query, search) {
                if (query === '') {
                    $result.addClass('d-none');
                } else {
                    $result.removeClass('d-none');
                    search(query);
                }
            }
        })
    );

    search.addWidget(
        Hits({
            container: $result.get(0),
            templates: {
                item: '<div>{{{_highlightResult.name.value}}}</div>'
            },
            cssClasses: {
                empty: 'd-none',
                root: 'card',
                item: 'result-item'
            }
        })
    );

    $input.one('keyup', () => {
        search.start();
        $input.focus();
    });
});
