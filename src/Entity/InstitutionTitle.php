<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InstitutionTitleRepository")
 * @ORM\Table(
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(name="institution_title_unique_institution_title", columns={"institution_id", "title_id"})
 *     }
 * )
 *
 * @UniqueEntity(fields={"institution", "title"})
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
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Institution",
     *     inversedBy="titles",
     *     fetch="EAGER"
     * )
     * @ORM\JoinColumn(nullable=false)
     */
    private $institution;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Title",
     *     fetch="EAGER"
     * )
     * @ORM\JoinColumn(nullable=false)
     */
    private $title;

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
}
