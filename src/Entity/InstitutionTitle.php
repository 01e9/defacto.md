<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InstitutionTitleRepository")
 * @ORM\Table(
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(name="institution_title_unique_institution_title", columns={"institution_id", "title_id"})
 *     }
 * )
 *
 * @UniqueEntity(fields={"institution", "title"}, errorPath="title")
 */
class InstitutionTitle
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=36)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Institution", inversedBy="titles", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $institution;

    /**
     * @ORM\ManyToOne(targetEntity="Title", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(max=255)
     * @Assert\Url()
     */
    private $prerogativesLink;

    public function getId() : ?string
    {
        return $this->id;
    }

    public function getInstitution() : ?Institution
    {
        return $this->institution;
    }

    public function setInstitution(?Institution $institution) : InstitutionTitle
    {
        $this->institution = $institution;

        return $this;
    }

    public function getTitle() : ?Title
    {
        return $this->title;
    }

    public function setTitle(?Title $title) : InstitutionTitle
    {
        $this->title = $title;

        return $this;
    }

    public function getPrerogativesLink() : ?string
    {
        return $this->prerogativesLink;
    }

    public function setPrerogativesLink(?string $prerogativesLink) : self
    {
        $this->prerogativesLink = $prerogativesLink;

        return $this;
    }
}
