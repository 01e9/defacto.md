import 'bootstrap-material-design'
import Chartist from 'chartist/dist/chartist.min'

jQuery(($) => {
    $('.chart').each((i, el) => {
        draw(
            $(el).data('chart-type'),
            el,
            $(el).data('chart-data') || {},
        );
    });

    function draw(type, el, data) {
        switch (type) {
            case 'promises-pie': {
                let chartistData = {
                    series: [],
                    labels: []
                };

                $.each(data.promises, (i, promise) => {
                    chartistData.series.push(promise.count);
                    chartistData.labels.push(promise.name);
                });

                el.classList.add('ct-chart');
                el.classList.add('ct-square');

                return new Chartist.Pie(el, chartistData);
            }
        }
    }
});