<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CandidateProblemOpinionRepository")
 * @ORM\Table(
 *     name="candidates_problems_opinions",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(
 *          name="candidate_problem_opinion_unique_composite",
 *          columns={"politician_id", "election_id", "constituency_id", "problem_id"}
 *      )
 *     }
 * )
 *
 * @UniqueEntity(fields={"politician", "election", "constituency", "problem"}, errorPath="problem")
 */
class CandidateProblemOpinion
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=36)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Politician")
     * @ORM\JoinColumn(nullable=false)
     */
    private $politician;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Election")
     * @ORM\JoinColumn(nullable=false)
     */
    private $election;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Constituency", inversedBy="candidateProblemOpinions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $constituency;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Problem")
     * @ORM\JoinColumn(nullable=false)
     */
    private $problem;

    /**
     * @var string
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="3", max="10000")
     */
    private $opinion;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPolitician(): ?Politician
    {
        return $this->politician;
    }

    public function setPolitician(?Politician $politician): self
    {
        $this->politician = $politician;

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

    public function getConstituency(): ?Constituency
    {
        return $this->constituency;
    }

    public function setConstituency(?Constituency $constituency): self
    {
        $this->constituency = $constituency;

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

    public function getOpinion(): ?string
    {
        return $this->opinion;
    }

    public function setOpinion(?string $opinion): self
    {
        $this->opinion = $opinion;

        return $this;
    }
}
