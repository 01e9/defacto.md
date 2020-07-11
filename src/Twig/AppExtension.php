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
        ];
    }

    function toAscii(string $string)
    {
        return $this->slugify->slugify($string, ' ');
    }
}
