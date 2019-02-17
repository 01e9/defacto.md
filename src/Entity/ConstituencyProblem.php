<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConstituencyProblemRepository")
 * @ORM\Table(
 *     name="constituency_problems",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(
 *          name="constituency_problems_unique_composite",
 *          columns={"constituency_id", "election_id", "problem_id", "type"}
 *      )
 *     }
 * )
 *
 * @UniqueEntity(fields={"constituency", "election", "problem", "type"}, errorPath="problem")
 */
class ConstituencyProblem
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=36)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Constituency", inversedBy="problems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $constituency;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Election", inversedBy="constituencyProblems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $election;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Problem", inversedBy="constituencyProblems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $problem;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=true)
     *
     * @Assert\GreaterThanOrEqual(1)
     */
    private $respondents;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     *
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(1)
     */
    private $percentage;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     *
     * @Assert\Choice({"local", "national"})
     */
    private $type;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getConstituency(): ?Constituency
    {
        return $this->constituency;
    }

    public function setConstituency(?Constituency $constituency): self
    {
        $this->constituency = $constituency;

        return $this;
    }

    public function getElection(): ?Election
    {
        return $this->election;
    }

    public function setElection(?Election $election): self
    {
        $this->election = $election;

        return $this;
    }

    public function getProblem(): ?Problem
    {
        return $this->problem;
    }

    public function setProblem(?Problem $problem): self
    {
        $this->problem = $problem;

        return $this;
    }

    public function getRespondents(): ?int
    {
        return $this->respondents;
    }

    public function setRespondents(?int $respondents): self
    {
        $this->respondents = $respondents;

        return $this;
    }

    public function getPercentage() : ?float
    {
        return $this->percentage;
    }

    public function setPercentage(?float $percentage) : self
    {
        $this->percentage = $percentage;

        return $this;
    }

    public function getType() : ?string
    {
        return $this->type;
    }

    public function setType(?string $type) : self
    {
        $this->type = $type;

        return $this;
    }
}
