<?php

namespace App\Filter;

use App\Consts;
use App\Entity\CompetenceCategory;
use App\Repository\CompetenceCategoryRepository;
use Doctrine\Common\Collections\Collection;

class MandateFilter
{
    const QUERY_FROM_DATE = 'from_date';
    const QUERY_TO_DATE = 'to_date';
    const QUERY_CATEGORY = 'category';

    public ?\DateTime $fromDate = null;
    public ?\DateTime $toDate = null;
    /** @var Collection|CompetenceCategory[]|null  */
    public ?Collection $categories = null;

    public function isEmpty(): bool
    {
        return !$this->fromDate && !$this->toDate && !$this->categories;
    }

    public static function createFromValidQueryArray(array $query, CompetenceCategoryRepository $categoryRepository): self
    {
        $filter = new self;

        if (
            !empty($query[self::QUERY_FROM_DATE]) &&
            ($fromData = \DateTime::createFromFormat(Consts::DATE_FORMAT_PHP, $query[self::QUERY_FROM_DATE]))
        ) {
            $filter->fromDate = $fromData;
        }
        if (
            !empty($query[self::QUERY_TO_DATE]) &&
            ($toDate = \DateTime::createFromFormat(Consts::DATE_FORMAT_PHP, $query[self::QUERY_TO_DATE]))
        ) {
            $filter->toDate = $toDate;
        }
        if (
            !empty($query[self::QUERY_CATEGORY]) &&
            ($category = $categoryRepository->findOneBy(['slug' => $query[self::QUERY_CATEGORY]]))
        ) {
            $filter->categories = $categoryRepository->findWithChildren($category);
        }

        return $filter;
    }
}