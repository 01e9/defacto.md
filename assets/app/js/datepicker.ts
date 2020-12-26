import "bootstrap-datepicker";
import $ from "jquery";

export function initElementDatePickers(element) {
    $(element).find('input.datepicker-input:not(.initialized)')
        .addClass('initialized')
        // @ts-ignore
        .datepicker({
            format: "dd.mm.yyyy",
            todayHighlight: true
        });
}
