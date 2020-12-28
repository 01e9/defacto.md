import "bootstrap-datepicker";
import "bootstrap-datepicker/js/locales/bootstrap-datepicker.ro";
import $ from "jquery";
import { LANG } from "~/config";

export function initElementDatePickers(element, options = {}) {
    $(element).find('input.datepicker-input:not(.initialized)')
        .addClass('initialized')
        // @ts-ignore
        .datepicker({
            format: "dd.mm.yyyy",
            todayHighlight: true,
            language: LANG,
            ...options
        });
}
