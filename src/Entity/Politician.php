<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PoliticianRepository")
 * @ORM\Table(
 *     name="politicians",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(name="politician_unique_slug", columns={"slug"})
 *     }
 * )
 *
 * @UniqueEntity(fields={"slug"})
 */
class Politician
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=36)
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @Assert\Uuid()
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"searchable"})
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=50)
     * @Assert\Regex(pattern="/^[\p{L}\d]+(\-[\p{L}\d]+)*$/u", message="invalid.slug")
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=20)
     * @Groups({"searchable"})
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=20)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=20)
     * @Groups({"searchable"})
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=20)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\File(mimeTypes={"image/jpeg", "image/png", "image/gif"})
     */
    private $photo;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthDate;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     *
     * @Assert\Length(max=10000)
     */
    private $studies;

    /**
     * @var string
     * @ORM\Column(type="string", length=120, nullable=true)
     *
     * @Assert\Length(max=120)
     */
    private $profession;

    /**
     * @var string
     * @ORM\Column(type="string", length=120, nullable=true)
     *
     * @Assert\Length(max=120)
     * @Assert\Url()
     */
    private $website;

    /**
     * @var string
     * @ORM\Column(type="string", length=120, nullable=true)
     *
     * @Assert\Length(max=120)
     * @Assert\Url()
     */
    private $facebook;

    /**
     * @var string
     * @ORM\Column(type="string", length=120, nullable=true)
     *
     * @Assert\Length(max=120)
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(type="string", length=16, nullable=true)
     *
     * @Assert\Length(max=16)
     */
    private $phone;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     *
     * @Assert\Length(max=10000)
     */
    private $previousTitles;

    /**
     * @ORM\OneToMany(targetEntity="Mandate", mappedBy="politician")
     */
    private $mandates;

    /**
     * @ORM\OneToMany(targetEntity="Candidate", mappedBy="politician")
     */
    private $candidates;

    public function __construct()
    {
        $this->mandates = new ArrayCollection();
        $this->candidates = new ArrayCollection();
    }

    public function getId() : ?string
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

    public function getFirstName() : ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName) : self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName() : ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName) : self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return File
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto($photo) : self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getStudies() : ?string
    {
        return $this->studies;
    }

    public function setStudies(?string $studies) : self
    {
        $this->studies = $studies;

        return $this;
    }

    public function getProfession() : ?string
    {
        return $this->profession;
    }

    public function setProfession(?string $profession) : self
    {
        $this->profession = $profession;

        return $this;
    }

    public function getWebsite() : ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website) : self
    {
        $this->website = $website;

        return $this;
    }

    public function getFacebook() : ?string
    {
        return $this->facebook;
    }

    public function setFacebook(?string $facebook) : self
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function getEmail() : ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email) : self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone() : ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone) : self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPreviousTitles() : ?string
    {
        return $this->previousTitles;
    }

    public function setPreviousTitles(?string $previousTitles) : self
    {
        $this->previousTitles = $previousTitles;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getCandidates()
    {
        return $this->candidates;
    }

    /**
     * @return ArrayCollection
     */
    public function getMandates()
    {
        return $this->mandates;
    }

    /**
     * @return Candidate[]
     */
    public function getSortedCandidates()
    {
        $candidates = $this->getCandidates()->toArray();

        uasort($candidates, function(Candidate $a, Candidate $b) {
            return ($a->getElection()->getDate() === $b->getElection()->getDate()) ? 0
                : ($a->getElection()->getDate() > $b->getElection()->getDate() ? -1 : 1);
        });

        return $candidates;
    }
}
