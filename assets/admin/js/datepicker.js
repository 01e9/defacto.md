import "bootstrap-datepicker"

jQuery($ => {
    $('input.datepicker-input').datepicker({
        format: "dd.mm.yyyy",
        todayHighlight: true
    });
})