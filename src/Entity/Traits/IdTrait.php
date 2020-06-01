<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait IdTrait
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=36)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    public function getId() : ?string
    {
        return $this->id;
    }
}
