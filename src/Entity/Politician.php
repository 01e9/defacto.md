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
     * @ORM\OneToMany(targetEntity="App\Entity\Mandate", mappedBy="politician")
     */
    private $mandates;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ConstituencyCandidate", mappedBy="politician")
     */
    private $constituencyCandidates;

    public function __construct()
    {
        $this->mandates = new ArrayCollection();
        $this->constituencyCandidates = new ArrayCollection();
    }

    public function getId() : ?string
    {
        return $this->id;
    }

    public function getSlug() : ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug) : Politician
    {
        $this->slug = $slug;

        return $this;
    }

    public function getFirstName() : ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName) : Politician
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName() : ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName) : Politician
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

    public function setPhoto($photo) : Politician
    {
        $this->photo = $photo;

        return $this;
    }
}
