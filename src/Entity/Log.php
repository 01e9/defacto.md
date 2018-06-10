<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LogRepository")
 * @ORM\Table(
 *     name="logs",
 *     indexes={
 *          @ORM\Index(name="log_index_object_type_and_id", columns={"object_type", "object_id"})
 *     }
 * )
 */
class Log
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=36)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\Column(name="occurred_time", type="datetime")
     */
    private $occurredTime;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $dataBefore;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $dataAfter;

    /**
     * @ORM\Column(type="string", length=36, nullable=true)
     */
    private $objectType;

    /**
     * @ORM\Column(type="string", length=36, nullable=true)
     */
    private $objectId;

    public function __construct()
    {
        $this->occurredTime = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getOccurredTime(): ?\DateTimeInterface
    {
        return $this->occurredTime;
    }

    public function setOccurredTime(\DateTimeInterface $occurredTime): self
    {
        $this->occurredTime = $occurredTime;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDataBefore(): ?string
    {
        return $this->dataBefore;
    }

    public function setDataBefore(?string $dataBefore): self
    {
        $this->dataBefore = $dataBefore;

        return $this;
    }

    public function getDataAfter(): ?string
    {
        return $this->dataAfter;
    }

    public function setDataAfter(?string $dataAfter): self
    {
        $this->dataAfter = $dataAfter;

        return $this;
    }

    public function getObjectType(): ?string
    {
        return $this->objectType;
    }

    public function setObjectType($objectType): self
    {
        $this->objectType = $objectType;

        return $this;
    }

    public function getObjectId(): ?string
    {
        return $this->objectId;
    }

    public function setObjectId($objectId): self
    {
        $this->objectId = $objectId;

        return $this;
    }
}
