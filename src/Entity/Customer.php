<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 */
class Customer implements UserInterface
{
    const ROLE_CUSTOMER = 'ROLE_CUSTOMER';
    const DEFAULT_ROLES = [self::ROLE_CUSTOMER];
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
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fullName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phone;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $confirmationToken;

//    /**
//     * @ORM\OneToMany(targetEntity=Address::class, mappedBy="user")
//     */
//    private $addresses;
//
//    /**
//     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="user")
//     */
    private $orders;

    public function __construct()
    {
        $this->roles = self::DEFAULT_ROLES;
        $this->enabled = false;
//        $this->addresses = new ArrayCollection();
//        $this->orders = new ArrayCollection();
    }

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
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken($confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }

//    /**
//     * @return Collection|Address[]
//     */
//    public function getAddresses(): Collection
//    {
//        return $this->addresses;
//    }
//
//    public function addAddress(Address $address): self
//    {
//        if (!$this->addresses->contains($address)) {
//            $this->addresses[] = $address;
//            $address->setUser($this);
//        }
//
//        return $this;
//    }
//
//    public function removeAddress(Address $address): self
//    {
//        if ($this->addresses->removeElement($address)) {
//            // set the owning side to null (unless already changed)
//            if ($address->getUser() === $this) {
//                $address->setUser(null);
//            }
//        }
//
//        return $this;
//    }
//
//    /**
//     * @return Collection|Order[]
//     */
//    public function getOrders(): Collection
//    {
//        return $this->orders;
//    }
//
//    public function addOrder(Order $order): self
//    {
//        if (!$this->orders->contains($order)) {
//            $this->orders[] = $order;
//            $order->setUser($this);
//        }
//
//        return $this;
//    }
//
//    public function removeOrder(Order $order): self
//    {
//        if ($this->orders->removeElement($order)) {
//            // set the owning side to null (unless already changed)
//            if ($order->getUser() === $this) {
//                $order->setUser(null);
//            }
//        }
//
//        return $this;
//    }
}
