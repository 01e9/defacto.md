<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MandateRepository")
 * @ORM\Table(
 *     name="mandates",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(
 *          name="mandate_unique_politician_institution_begin",
 *          columns={"politician_id", "institution_title_id", "begin_date"}
 *      ),
 *      @ORM\UniqueConstraint(
 *          name="mandate_unique_politician_institution_end",
 *          columns={"politician_id", "institution_title_id", "end_date"}
 *      )
 *     }
 * )
 *
 * @UniqueEntity(fields={"politician", "institutionTitle", "beginDate"}, errorPath="beginDate")
 * @UniqueEntity(fields={"politician", "institutionTitle", "endDate"}, errorPath="endDate")
 */
class Mandate
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=36)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="date")
     *
     * @Assert\NotBlank()
     */
    private $beginDate;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="date")
     *
     * @Assert\NotBlank()
     * @Assert\Expression(expression="this.getBeginDate() < this.getEndDate()", message="invalid.date_range")
     */
    private $endDate;

    /**
     * @var Election
     * @ORM\ManyToOne(targetEntity="App\Entity\Election", inversedBy="mandates", fetch="EAGER")
     * @ORM\JoinColumn(name="election_id", nullable=false)
     *
     * @Assert\NotBlank()
     */
    private $election;

    /**
     * @var Constituency
     * @ORM\ManyToOne(targetEntity="App\Entity\Constituency", inversedBy="mandates")
     * @ORM\JoinColumn(name="constituency_id", nullable=true)
     */
    private $constituency;

    /**
     * @var Politician
     * @ORM\ManyToOne(targetEntity="App\Entity\Politician", inversedBy="mandates", fetch="EAGER")
     * @ORM\JoinColumn(name="politician_id", nullable=true)
     */
    private $politician;

    /**
     * @var InstitutionTitle
     * @ORM\ManyToOne(targetEntity="App\Entity\InstitutionTitle")
     * @ORM\JoinColumn(name="institution_title_id", nullable=false)
     *
     * @Assert\NotBlank()
     */
    private $institutionTitle;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"unsigned"=true})
     *
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(0)
     */
    private $votesCount;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=5, scale=2)
     *
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(0)
     */
    private $votesPercent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(max=255)
     * @Assert\Url()
     */
    private $decisionLink;

    public function getId() : ?string
    {
        return $this->id;
    }

    public function getBeginDate() : ?\DateTime
    {
        return $this->beginDate;
    }

    public function setBeginDate(?\DateTime $date) : self
    {
        $this->beginDate = $date;

        return $this;
    }

    public function getEndDate() : ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTime $date) : self
    {
        $this->endDate = $date;

        return $this;
    }

    public function getElection() : ?Election
    {
        return $this->election;
    }

    public function setElection(?Election $election) : self
    {
        $this->election = $election;

        return $this;
    }

    public function getPolitician() : ?Politician
    {
        return $this->politician;
    }

    public function setPolitician(?Politician $politician) : self
    {
        $this->politician = $politician;

        return $this;
    }

    public function getConstituency() : ?Constituency
    {
        return $this->constituency;
    }

    public function setConstituency(?Constituency $constituency) : self
    {
        $this->constituency = $constituency;

        return $this;
    }

    public function getInstitutionTitle() : ?InstitutionTitle
    {
        return $this->institutionTitle;
    }

    public function setInstitutionTitle(?InstitutionTitle $institutionTitle) : self
    {
        $this->institutionTitle = $institutionTitle;

        return $this;
    }

    public function getVotesCount() : ?int
    {
        return $this->votesCount;
    }

    public function setVotesCount(?int $count) : self
    {
        $this->votesCount = $count;

        return $this;
    }

    public function getVotesPercent() : ?float
    {
        return $this->votesPercent;
    }

    public function setVotesPercent(?float $percent) : self
    {
        $this->votesPercent = $percent;

        return $this;
    }

    public function getDecisionLink() : ?string
    {
        return $this->decisionLink;
    }

    public function setDecisionLink(?string $decisionLink) : self
    {
        $this->decisionLink = $decisionLink;

        return $this;
    }

    public function getChoiceName() : string
    {
        $dateFormat = 'd.m.Y';

        return (
            ($this->getPolitician()
                ? ($this->getPolitician()->getFirstName() . ' ' . $this->getPolitician()->getLastName()) . ' / '
                : ''
            ) . (
                $this->getBeginDate()->format($dateFormat)
                . ' - ' .
                $this->getEndDate()->format($dateFormat)
            )
            . ' / ' . $this->getInstitutionTitle()->getTitle()->getName()
        );
    }
}
