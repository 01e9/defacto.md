import "bootstrap-datepicker";
import $ from "jquery";
import { initElementDatePickers } from "../../../common/js/datepicker";

$(() => {
    initElementDatePickers(document.body);
});
