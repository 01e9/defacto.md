<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait DescriptionTrait
{
    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Assert\Length(min=1, max=10000)
     */
    private $description;

    public function getDescription() : ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description)
    {
        $this->description = $description;

        return $this;
    }
}
