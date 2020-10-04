<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BlogPostRepository")
 * @ORM\Table(
 *     name="blog_posts",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(name="blog_posts_unique_slug", columns={"slug"})
 *     }
 * )
 *
 * @UniqueEntity(fields={"slug"})
 */
class BlogPost
{
    use Traits\IdTrait;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private $publishTime;

    /**
     * @ORM\Column(type="string", length=120)
     * @Groups({"searchable"})
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=120)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=120)
     * @Groups({"searchable"})
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=120)
     * @Assert\Regex(pattern="/^[a-z\d]+(\-[a-z\d]+)*$/", message="invalid.slug")
     */
    private $slug;

    /**
     * note: can't use fetch="EAGER" because of form unable to save select value to NULL
     * @ORM\ManyToOne(targetEntity="App\Entity\BlogCategory", inversedBy="posts")
     * @ORM\JoinColumn(nullable=true)
     */
    private $category;

    /**
     * @var string
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="100", max="10000")
     */
    private $content;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $image;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCategory(): ?BlogCategory
    {
        return $this->category;
    }

    public function setCategory(?BlogCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublishTime() : ?\DateTime
    {
        return $this->publishTime;
    }

    public function setPublishTime(?\DateTime $date) : self
    {
        $this->publishTime = $date;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image) : self
    {
        $this->image = $image;

        return $this;
    }
}
