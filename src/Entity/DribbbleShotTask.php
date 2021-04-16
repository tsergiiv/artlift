<?php

namespace App\Entity;

use App\Repository\DribbbleShotTaskRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DribbbleShotTaskRepository::class)
 */
class DribbbleShotTask
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="integer")
     */
    private $countLikes;

    /**
     * @ORM\Column(type="integer")
     */
    private $payed;

    /**
     * @ORM\Column(type="float")
     */
    private $ammount;

    /**
     * @ORM\Column(type="text")
     */
    private $sessionId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $shot;

    /**
     * @ORM\Column(type="integer")
     */
    private $countComments;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $likesPassed;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $commentsPassed;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountLikes(): ?int
    {
        return $this->countLikes;
    }

    public function setCountLikes(int $countLikes): self
    {
        $this->countLikes = $countLikes;

        return $this;
    }

    public function getPayed(): ?int
    {
        return $this->payed;
    }

    public function setPayed(int $payed): self
    {
        $this->payed = $payed;

        return $this;
    }

    public function getAmmount(): ?float
    {
        return $this->ammount;
    }

    public function setAmmount(float $ammount): self
    {
        $this->ammount = $ammount;

        return $this;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function setSessionId(string $sessionId): self
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    public function getShot(): ?string
    {
        return $this->shot;
    }

    public function setShot(string $shot): self
    {
        $this->shot = $shot;

        return $this;
    }

    public function getCountComments(): ?int
    {
        return $this->countComments;
    }

    public function setCountComments(int $countComments): self
    {
        $this->countComments = $countComments;

        return $this;
    }

    public function getLikesPassed(): ?int
    {
        return $this->likesPassed;
    }

    public function setLikesPassed(?int $likesPassed): self
    {
        $this->likesPassed = $likesPassed;

        return $this;
    }

    public function getCommentsPassed(): ?int
    {
        return $this->commentsPassed;
    }

    public function setCommentsPassed(?int $commentsPassed): self
    {
        $this->commentsPassed = $commentsPassed;

        return $this;
    }
}
