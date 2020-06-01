import "bootstrap-datepicker";
import $ from "jquery";

export function initElementDatePickers(element) {
    // @ts-ignore
    $(element).find('input.datepicker-input:not(.initialized)').addClass('initialized').datepicker({
        format: "dd.mm.yyyy",
        todayHighlight: true
    });
}
