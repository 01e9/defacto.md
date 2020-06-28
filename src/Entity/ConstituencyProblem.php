<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConstituencyProblemRepository")
 * @ORM\Table(
 *     name="constituency_problems",
 *     indexes={
 *      @ORM\Index(
 *          name="constituency_problem_index_constituency_percentage",
 *          columns={"constituency_id", "percentage"}
 *      )
 *     },
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
    use Traits\IdTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Constituency", inversedBy="problems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $constituency;

    /**
     * @ORM\ManyToOne(targetEntity="Election", inversedBy="constituencyProblems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $election;

    /**
     * @ORM\ManyToOne(targetEntity="Problem", inversedBy="constituencyProblems")
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
     * @Assert\GreaterThanOrEqual(1)
     */
    private $percentage;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     *
     * @Assert\Choice({"local", "national"})
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(max=255)
     * @Assert\Url()
     */
    private $questionnaireEmbedLink;

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

    public function getQuestionnaireEmbedLink(): ?string
    {
        return $this->questionnaireEmbedLink;
    }

    public function setQuestionnaireEmbedLink(?string $questionnaireEmbedLink): self
    {
        $this->questionnaireEmbedLink = $questionnaireEmbedLink;

        return $this;
    }
}
