<?php

namespace App;

use App\Entity\BlogPost;
use App\Entity\Party;
use App\Entity\Politician;

final class Consts
{
    const DATE_FORMAT_INTL = 'dd.MM.yyyy';
    const DATE_FORMAT_PHP = 'd.m.Y';
    const TIME_FORMAT_PHP = 'H:i:s';

    const TWIG_SCHEMA_DATE_FORMAT = 'Y-m-d';

    const UPLOADS_DIR_POLITICIAN = '/uploads/politicians/';
    const UPLOADS_DIR_PARTY = '/uploads/parties/';
    const UPLOADS_DIR_BLOG = '/uploads/blog/';

    const ENTITY_UPLOAD_DIRS = [
        Politician::class => self::UPLOADS_DIR_POLITICIAN,
        Party::class => self::UPLOADS_DIR_PARTY,
        BlogPost::class => self::UPLOADS_DIR_BLOG,
    ];

    const ENTITY_FILE_FIELDS = [
        Politician::class => 'photo',
        Party::class => 'logo',
        BlogPost::class => 'image',
    ];

    const COMPETENCE_USE_MULTIPLICATION_FACTOR = 2;

    const CATEGORY_SEPARATOR = '►';

    const QUERY_PARAM_PAGE = 'page';
    const QUERY_PARAM_CATEGORY = 'category';

    const ADMIN_PAGINATION_SIZE_BLOG = 20;
    const ADMIN_PAGINATION_SIZE_METHODOLOGIES = 10;
    const ADMIN_PAGINATION_SIZE_PROMISES = 10;

    const PAGINATION_SIZE_BLOG = 12;
    const PAGINATION_SIZE_ACTIONS = 9;
    const PAGINATION_SIZE_MANDATES = 20;
    const PAGINATION_SIZE_PROMISES = 9;
}