<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConstituencyRepository")
 * @ORM\Table(
 *     name="constituencies",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(name="constituency_unique_slug", columns={"slug"})
 *     }
 * )
 *
 * @UniqueEntity(fields={"slug"})
 */
class Constituency
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
     * @Assert\Regex(pattern="/^[\p{L}\d]+(\-[\p{L}\d]+)*$/u", message="invalid.slug")
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=120)
     * @Groups({"searchable"})
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=120)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(min=3)
     * @Assert\Url()
     */
    private $link;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $map;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Mandate", mappedBy="constituency")
     */
    private $mandates;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ConstituencyProblem", mappedBy="constituency", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\Valid()
     */
    private $problems;

    /**
     * @ORM\OneToMany(targetEntity="Candidate", mappedBy="constituency", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\Valid()
     */
    private $candidates;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CandidateProblemOpinion", mappedBy="constituency", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\Valid()
     */
    private $candidateProblemOpinions;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=true)
     *
     * @Assert\GreaterThanOrEqual(1)
     */
    private $number;

    public function __construct()
    {
        $this->mandates = new ArrayCollection();
        $this->problems = new ArrayCollection();
        $this->candidates = new ArrayCollection();
        $this->candidateProblemOpinions = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getSlug() : ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug) : self
    {
        $this->slug = $slug;

        return $this;
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

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getMap(): ?array
    {
        return $this->map;
    }

    public function setMap(?array $map): self
    {
        $this->map = $map;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(?int $number): self
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return ArrayCollection|ConstituencyProblem[]
     */
    public function getProblems()
    {
        return $this->problems;
    }

    public function setProblems($problems): self
    {
        $this->problems = $problems;

        return $this;
    }

    /**
     * @return ArrayCollection|Candidate[]
     */
    public function getCandidates()
    {
        return $this->candidates;
    }

    public function setCandidates($candidates): self
    {
        $this->candidates = $candidates;

        return $this;
    }

    /**
     * @return ArrayCollection|CandidateProblemOpinion[]
     */
    public function getCandidateProblemOpinions()
    {
        return $this->candidateProblemOpinions;
    }

    public function setCandidateProblemOpinions($candidateProblemOpinions): self
    {
        $this->candidateProblemOpinions = $candidateProblemOpinions;

        return $this;
    }
}
