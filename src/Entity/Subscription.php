<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubscriptionRepository::class)
 */
class Subscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="decimal", precision=8, scale=2)
     */
    private $price;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_shot;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $likes;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $comments;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $subscribers;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getIsShot(): ?bool
    {
        return $this->is_shot;
    }

    public function setIsShot(bool $is_shot): self
    {
        $this->is_shot = $is_shot;

        return $this;
    }

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(?int $likes): self
    {
        $this->likes = $likes;

        return $this;
    }

    public function getComments(): ?int
    {
        return $this->comments;
    }

    public function setComments(?int $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    public function getSubscribers(): ?int
    {
        return $this->subscribers;
    }

    public function setSubscribers(?int $subscribers): self
    {
        $this->subscribers = $subscribers;

        return $this;
    }
}
