import "bootstrap-datepicker";
import $ from "jquery";
import { initElementDatePickers } from "../../js/datepicker";

$(() => {
    initElementDatePickers(document.body);
});
