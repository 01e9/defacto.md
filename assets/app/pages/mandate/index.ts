import "bootstrap-datepicker";
import $ from "jquery";
import { initElementDatePickers } from "../../../common/js/datepicker";

$(() => {
    initElementDatePickers(document.body, {
        endDate: new Date(new Date().getTime()+1000*60*60*24)
    });
});
