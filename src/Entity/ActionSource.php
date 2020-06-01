<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActionSourceRepository")
 * @ORM\Table(
 *     name="action_sources",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(name="action_source_unique_name", columns={"action_id", "name"})
 *     }
 * )
 *
 * @UniqueEntity(fields={"action", "name"}, errorPath="name")
 */
class ActionSource
{
    use Traits\IdTrait;

    /**
     * @ORM\Column(type="string", length=120)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=120)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     * @Assert\Url()
     */
    private $link;

    /**
     * @ORM\ManyToOne(targetEntity="Action", inversedBy="sources")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     */
    private $action;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getAction() : ?Action
    {
        return $this->action;
    }

    public function setAction(?Action $action) : self
    {
        $this->action = $action;

        return $this;
    }
}
