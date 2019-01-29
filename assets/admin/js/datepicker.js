import "bootstrap-datepicker"

document.body.addEventListener("app:initElement", (e) => {
    $(e.detail).find('input.datepicker-input').datepicker({
        format: "dd.mm.yyyy",
        todayHighlight: true
    });
});
