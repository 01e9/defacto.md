<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(
 *     name="users",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(name="user_unique_email", columns={"email"})
 *     }
 * )
 */
class User implements UserInterface, EquatableInterface, \Serializable
{
    use Traits\IdTrait;

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
    private $isActive = false;

    /**
     * @ORM\Column(name="roles", type="simple_array", options={"default"="ROLE_USER"})
     */
    private $roles = ["ROLE_USER" /* todo: const */];

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

    public function isEnabled()
    {
        return $this->isActive;
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($this->getUsername() !== $user->getUsername()) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->isActive !== $user->getIsActive()) {
            return false;
        }

        return true;
    }
}
