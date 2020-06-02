<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait NameTrait
{
    /**
     * @ORM\Column(type="string", length=120)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=120)
     */
    private $name;

    public function getName() : ?string
    {
        return $this->name;
    }

    public function setName(?string $name) : self
    {
        $this->name = $name;

        return $this;
    }
}
