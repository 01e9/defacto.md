<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Cocur\Slugify\Slugify;

class AppExtension extends AbstractExtension
{
    private Slugify $slugify;

    public function __construct()
    {
        $this->slugify = new Slugify();
    }

    public function getFilters()
    {
        return [
            new TwigFilter('ascii', [$this, 'toAscii']),
            new TwigFilter('transpoints', [$this, 'toPointsToTransCount']),
        ];
    }

    function toAscii(string $string)
    {
        return $this->slugify->slugify($string, ' ');
    }

    function toPointsToTransCount(string $string)
    {
        if (!is_numeric($string)) {
            return $string;
        }

        $count = floatval($string);

        if ($count > 1 && $count < 2) {
            return 2;
        }

        return $count;
    }
}
