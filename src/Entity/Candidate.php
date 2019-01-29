<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CandidateRepository")
 * @ORM\Table(
 *     name="candidates",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(
 *          name="candidates_unique_composite",
 *          columns={"politician_id", "election_id", "constituency_id"}
 *      )
 *     }
 * )
 *
 * @UniqueEntity(fields={"politician", "election", "constituency"}, errorPath="politician")
 */
class Candidate
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=36)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Politician", inversedBy="candidates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $politician;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Election", inversedBy="candidates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $election;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Constituency", inversedBy="candidates")
     * @ORM\JoinColumn(nullable=true)
     */
    private $constituency;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Party")
     * @ORM\JoinColumn(nullable=true)
     */
    private $party;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="date", nullable=true)
     */
    private $registrationDate;

    /**
     * @var string
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $registrationNote;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(min=3)
     * @Assert\Url()
     */
    private $registrationLink;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Assert\Length(min=3, max=10000)
     */
    private $electoralPlatform;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(min=3)
     * @Assert\Url()
     */
    private $electoralPlatformLink;

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

    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(?\DateTimeInterface $registrationDate): self
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    public function getRegistrationNote() : ?string
    {
        return $this->registrationNote;
    }

    public function setRegistrationNote(?string $registrationNote) : self
    {
        $this->registrationNote = $registrationNote;

        return $this;
    }

    public function getRegistrationLink() : ?string
    {
        return $this->registrationLink;
    }

    public function setRegistrationLink(?string $registrationLink) : self
    {
        $this->registrationLink = $registrationLink;

        return $this;
    }

    public function getElectoralPlatform() : ?string
    {
        return $this->electoralPlatform;
    }

    public function setElectoralPlatform(?string $electoralPlatform) : self
    {
        $this->electoralPlatform = $electoralPlatform;

        return $this;
    }

    public function getElectoralPlatformLink() : ?string
    {
        return $this->electoralPlatformLink;
    }

    public function setElectoralPlatformLink(?string $electoralPlatformLink) : self
    {
        $this->electoralPlatformLink = $electoralPlatformLink;

        return $this;
    }
}
