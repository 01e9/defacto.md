<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PromiseActionSourceRepository")
 * @ORM\Table(
 *     name="promise_action_sources",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(name="promise_action_source_unique_name", columns={"action_id", "name"})
 *     }
 * )
 *
 * @UniqueEntity(fields={"action", "name"}, errorPath="name")
 */
class PromiseActionSource
{
    use Traits\IdTrait;
    use Traits\NameTrait;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     * @Assert\Url()
     */
    private $link;

    /**
     * @ORM\ManyToOne(targetEntity="PromiseAction", inversedBy="sources")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     */
    private $action;

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getAction() : ?PromiseAction
    {
        return $this->action;
    }

    public function setAction(?PromiseAction $action) : self
    {
        $this->action = $action;

        return $this;
    }
}
