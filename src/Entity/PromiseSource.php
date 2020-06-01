<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PromiseSourceRepository")
 * @ORM\Table(
 *     name="promise_sources",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(name="promise_source_unique_name", columns={"promise_id", "name"})
 *     }
 * )
 *
 * @UniqueEntity(fields={"promise", "name"}, errorPath="name")
 */
class PromiseSource
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
     * @ORM\ManyToOne(targetEntity="Promise", inversedBy="sources")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     */
    private $promise;

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

    public function getPromise() : ?Promise
    {
        return $this->promise;
    }

    public function setPromise(?Promise $promise) : self
    {
        $this->promise = $promise;

        return $this;
    }
}
