<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $full_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $googleid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $facebookid;

    /**
     * @ORM\OneToMany(targetEntity=DribbleSubscriptionTask::class, mappedBy="user")
     */
    private $subscriptions;

    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
        $this->dribbbleShotTasks = new ArrayCollection();
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dribbbleLogin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dribbblePassword;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dribbbleAccount;

    /**
     * @ORM\OneToMany(targetEntity=DribbbleShotTask::class, mappedBy="user")
     */
    private $dribbbleShotTasks;

    /**
     * @ORM\OneToOne(targetEntity=BillingAddress::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $billingAddress;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->full_name;
    }

    public function setFullName(string $full_name): self
    {
        $this->full_name = $full_name;

        return $this;
    }

    public function getGoogleid(): ?string
    {
        return $this->googleid;
    }

    public function setGoogleid(?string $googleid): self
    {
        $this->googleid = $googleid;

        return $this;
    }

    public function getFacebookid(): ?string
    {
        return $this->facebookid;
    }

    public function setFacebookid(?string $facebookid): self
    {
        $this->facebookid = $facebookid;

        return $this;
    }

    public function getDribbbleLogin(): ?string
    {
        return $this->dribbbleLogin;
    }

    public function setDribbbleLogin(?string $dribbbleLogin): self
    {
        $this->dribbbleLogin = $dribbbleLogin;

        return $this;
    }

    public function getDribbblePassword(): ?string
    {
        return $this->dribbblePassword;
    }

    public function setDribbblePassword(?string $dribbblePassword): self
    {
        $this->dribbblePassword = $dribbblePassword;

        return $this;
    }

    public function getDribbbleAccount(): ?string
    {
        return $this->dribbbleAccount;
    }

    public function setDribbbleAccount(?string $dribbbleAccount): self
    {
        $this->dribbbleAccount = $dribbbleAccount;

        return $this;
    }

    /**
     * @return Collection|DribbleSubscriptionTask[]
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(DribbleSubscriptionTask $subscription): self
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions[] = $subscription;
            $subscription->setUser($this);
        }

        return $this;
    }

    public function removeSubscription(DribbleSubscriptionTask $subscription): self
    {
        if ($this->subscriptions->removeElement($subscription)) {
            // set the owning side to null (unless already changed)
            if ($subscription->getUser() === $this) {
                $subscription->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|DribbbleShotTask[]
     */
    public function getDribbbleShotTasks(): Collection
    {
        return $this->dribbbleShotTasks;
    }

    public function addDribbbleShotTask(DribbbleShotTask $dribbbleShotTask): self
    {
        if (!$this->dribbbleShotTasks->contains($dribbbleShotTask)) {
            $this->dribbbleShotTasks[] = $dribbbleShotTask;
            $dribbbleShotTask->setUser($this);
        }

        return $this;
    }

    public function removeDribbbleShotTask(DribbbleShotTask $dribbbleShotTask): self
    {
        if ($this->dribbbleShotTasks->removeElement($dribbbleShotTask)) {
            // set the owning side to null (unless already changed)
            if ($dribbbleShotTask->getUser() === $this) {
                $dribbbleShotTask->setUser(null);
            }
        }

        return $this;
    }

    public function getBillingAddress(): ?BillingAddress
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(BillingAddress $billingAddress): self
    {
        // set the owning side of the relation if necessary
        if ($billingAddress->getUser() !== $this) {
            $billingAddress->setUser($this);
        }

        $this->billingAddress = $billingAddress;

        return $this;
    }
}
