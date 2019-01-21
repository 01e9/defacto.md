<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConstituencyCandidateRepository")
 * @ORM\Table(
 *     name="constituency_candidates",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(
 *          name="constituency_candidates_unique_composite",
 *          columns={"constituency_id", "election_id", "politician_id"}
 *      )
 *     }
 * )
 *
 * @UniqueEntity(fields={"constituency", "election", "politician"}, errorPath="politician")
 */
class ConstituencyCandidate
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=36)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Constituency", inversedBy="candidates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $constituency;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Election", inversedBy="constituencyCandidates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $election;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Politician", inversedBy="constituencyCandidates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $politician;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Party")
     * @ORM\JoinColumn(nullable=true)
     */
    private $party;

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

    public function getPolitician(): ?Politician
    {
        return $this->politician;
    }

    public function setPolitician(?Politician $politician): self
    {
        $this->politician = $politician;

        return $this;
    }

    public function getParty() : ?Party
    {
        return $this->party;
    }

    public function setParty(?Party $party) : self
    {
        $this->party = $party;

        return $this;
    }
}
