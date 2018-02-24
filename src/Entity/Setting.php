<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SettingRepository")
 * @ORM\Table(name="settings")
 */
class Setting
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=100)
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $value;

    public function getId() : string
    {
        return $this->id;
    }

    public function setId(string $id) : Setting
    {
        $this->id = $id;

        return $this;
    }

    public function getValue() : string
    {
        return $this->value;
    }

    public function setValue(string $value) : Setting
    {
        $this->value = $value;

        return $this;
    }
}
