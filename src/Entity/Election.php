<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ElectionRepository")
 * @ORM\Table(
 *     name="elections",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(name="election_unique_slug", columns={"slug"})
 *     }
 * )
 *
 * @UniqueEntity(fields={"slug"})
 */
class Election
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=36)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=120)
     * @Groups({"searchable"})
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=120)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=120)
     * @Groups({"searchable"})
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=120)
     * @Assert\Regex(pattern="/^[\p{L}\d]+(\-[\p{L}\d]+)*$/u", message="invalid.slug")
     */
    private $slug;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="date")
     *
     * @Assert\NotBlank()
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Mandate", mappedBy="election")
     */
    private $mandates;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ConstituencyProblem", mappedBy="election")
     */
    private $constituencyProblems;

    /**
     * @ORM\OneToMany(targetEntity="Candidate", mappedBy="election")
     */
    private $candidates;

    public function __construct()
    {
        $this->mandates = new ArrayCollection();
        $this->constituencyProblems = new ArrayCollection();
        $this->candidates = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
