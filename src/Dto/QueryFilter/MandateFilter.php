<?php

namespace App\Dto\QueryFilter;

use App\Consts;

class MandateFilter
{
    public ?\DateTime $fromDate;

    public ?\DateTime $toDate;

    public static function createFromValidQueryArray(array $query): self
    {
        $filter = new self;

        $filter->fromDate = empty($query[Consts::QUERY_FILTER_FROM_DATE])
            ? null
            : (\DateTime::createFromFormat(Consts::DATE_FORMAT_PHP, $query[Consts::QUERY_FILTER_FROM_DATE]) ?: null);
        $filter->toDate = empty($query[Consts::QUERY_FILTER_TO_DATE])
            ? null
            : (\DateTime::createFromFormat(Consts::DATE_FORMAT_PHP, $query[Consts::QUERY_FILTER_TO_DATE]) ?: null)
    ;

        return $filter;
    }
}