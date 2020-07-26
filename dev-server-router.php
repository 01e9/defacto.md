<?php

do {
    if (preg_match('/\/sitemap.*\.xml$/', $_SERVER["REQUEST_URI"])) {
        break;
    }

    if (preg_match('/\.[a-z]{1,5}$/', $_SERVER["REQUEST_URI"])) {
        return false;
    }
} while(false);

require __DIR__ . "/public/index.php";
