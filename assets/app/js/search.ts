import InstantSearch from 'instantsearch.js/es/lib/InstantSearch'
import Hits from 'instantsearch.js/es/widgets/hits/hits'
import SearchBox from 'instantsearch.js/es/widgets/search-box/search-box'
import { IS_PRODUCTION, ALGOLIA_APP_ID, ALGOLIA_API_KEY, LANG } from "../../config";

const selectors = {
    $input: type => $('#search-'+ type +'-input'),
    $hits: type => $('#search-'+ type)
};

function createInstantSearchInstances(types) {
    let instances = {};

    types.forEach((type, i) => {
        let instance = new InstantSearch({
            appId: ALGOLIA_APP_ID,
            apiKey: ALGOLIA_API_KEY,
            indexName: (IS_PRODUCTION ? 'prod_' : 'dev_') + type.id,
            searchParameters: {
                hitsPerPage: 10
            }
        });

        instance.addWidget(
            SearchBox({
                container: selectors.$input(type.id).get(0),
                autofocus: false,
                magnifier: false,
                reset: false,
                wrapInput: false
            })
        );
        instance.addWidget(
            Hits({
                container: selectors.$hits(type.id).get(0),
                templates: {
                    item: type.id == "politicians"
                        ? '<a href="/'+ LANG +'/'+ type.path +'">{{{_highlightResult.firstName.value}}} {{{_highlightResult.lastName.value}}}</a>'
                        : '<a href="/'+ LANG +'/'+ type.path +'">{{{_highlightResult.name.value}}}</a>',
                    empty: '<span class="fa fa-minus-circle"></span>'
                },
                cssClasses: {
                    empty: 'text-muted pt-3',
                    root: 'list-group',
                    item: 'list-group-item'
                }
            })
        );

        instances[type.id] = instance;
    });

    return instances;
}

function instantSearchStart(type, instances) {
    if (instances[type].started) {
        return instances[type];
    }
    instances[type].start();
}

function focusFirstVisibleInput($modal) {
    $modal.find('.modal-header input[type="text"]:visible:first').focus();
}

function showOnlyInputForType(type, $modal) {
    $modal.find('.modal-header input[type="text"]').addClass('d-none');
    selectors.$input(type).removeClass('d-none');
}

const onTypeChange = {
    callback: ($modal, callback) => {
        $modal.find('.modal-body .nav').on('show.bs.tab', (event) => {
            const
                oldType = $(event.relatedTarget).attr('data-search-type'),
                newType = $(event.target).attr('data-search-type');

            callback(newType, oldType);
        });
    },
    syncQuery: ($modal, instantSearchInstances) => {
        onTypeChange.callback($modal, (newType, oldType) => {
            instantSearchInstances[newType].helper.setQuery(
                instantSearchInstances[oldType].helper.getQuery().query
            );
            instantSearchInstances[newType].refresh();
        });
    },
    startSearch: ($modal, instantSearchInstances) => {
        onTypeChange.callback($modal, (newType) => {
            instantSearchStart(newType, instantSearchInstances);
        });
    },
    showOnlyCurrentTypeInput: ($modal) => {
        onTypeChange.callback($modal, (newType) => {
            showOnlyInputForType(newType, $modal);
        });
    }
};

jQuery(function ($) {
    const $modal = $('#instant-search-modal');

    if (!$modal.length) {
        return console.log('Skipped search modal init');
    }

    const types = JSON.parse($modal.attr('data-search-types'));
    let instantSearchInstances = {};

    $modal
        .on('shown.bs.modal', () => {
            focusFirstVisibleInput($modal);
        })
        .one('show.bs.modal', () => {
            instantSearchInstances = createInstantSearchInstances(types);
            instantSearchStart(types[0].id, instantSearchInstances);
            onTypeChange.startSearch($modal, instantSearchInstances);
            onTypeChange.showOnlyCurrentTypeInput($modal);
            onTypeChange.syncQuery($modal, instantSearchInstances);
        });
});
