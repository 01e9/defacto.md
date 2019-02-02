import "bootstrap-datepicker"

export function initElementDatePickers(element) {
    $(element).find('input.datepicker-input:not(.initialized)').addClass('initialized').datepicker({
        format: "dd.mm.yyyy",
        todayHighlight: true
    });
}
