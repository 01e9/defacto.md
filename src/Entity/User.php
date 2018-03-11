<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(
 *     name="users",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(name="user_unique_email", columns={"email"})
 *     }
 * )
 */
class User implements AdvancedUserInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=36)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $salt;

    /**
     * @ORM\Column(name="is_active", type="boolean", options={"default"=0})
     */
    private $isActive;

    /**
     * @ORM\Column(name="roles", type="simple_array", options={"default"="ROLE_USER"})
     */
    private $roles;

    public function getId() : string
    {
        return $this->id;
    }

    public function setId(?string $id)
    {
        $this->id = $id;

        return $this;
    }

    public function getEmail() : string
    {
        return $this->email;
    }

    public function setEmail(?string $email)
    {
        $this->email = $email;

        return $this;
    }

    public function getIsActive() : bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getPassword() : string
    {
        return $this->password;
    }

    public function setPassword(?string $password)
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt() : string
    {
        return $this->salt;
    }

    public function setSalt(?string $salt)
    {
        $this->salt = $salt;

        return $this;
    }

    public function eraseCredentials()
    {
    }

    public function getUsername() : string
    {
        return $this->getEmail();
    }

    public function getRoles() : array
    {
        return $this->roles;
    }

    public function setRoles(?array $roles)
    {
        $this->roles = $roles;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->email,
            $this->password,
            $this->salt,
            $this->isActive
        ]);
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->password,
            $this->salt,
            $this->isActive
        ) = unserialize($serialized);
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }
}
