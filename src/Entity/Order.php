<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="orders")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $customer;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $createdAt;

	/**
	 * @ORM\Column(type="float")
	 */
	private $deliveryPrice;

	/**
	 * @ORM\OneToMany(targetEntity=OrderDetails::class, mappedBy="myOrder")
	 */
	private $orderDetails;

	/** @ORM\Column(type="json", nullable=true) */
	private $marking;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $stripeSessionId;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $reference;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $shippingFirstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $shippingLastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $shippingAddress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $shippingCity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $shippingProvince;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $shippingPostalCode;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $shippingLat;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $shippingLng;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $shippingEmail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $shippingPhone;


	public function __construct()
	{
		$this->orderDetails = new ArrayCollection();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

	public function getCustomer(): ?Customer
	{
		return $this->customer;
	}

	public function setCustomer(?Customer $customer): self
	{
		$this->customer = $customer;

		return $this;
	}

	public function getCreatedAt(): ?\DateTimeInterface
	{
		return $this->createdAt;
	}

	public function setCreatedAt(\DateTimeInterface $createdAt): self
	{
		$this->createdAt = $createdAt;

		return $this;
	}

	public function getDeliveryPrice(): ?float
	{
		return $this->deliveryPrice;
	}

	public function setDeliveryPrice(float $deliveryPrice): self
	{
		$this->deliveryPrice = $deliveryPrice;

		return $this;
	}

	/**
	 * @return Collection|OrderDetails[]
	 */
	public function getOrderDetails(): Collection
	{
		return $this->orderDetails;
	}

	public function addOrderDetail(OrderDetails $orderDetail): self
	{
		if (!$this->orderDetails->contains($orderDetail)) {
			$this->orderDetails[] = $orderDetail;
			$orderDetail->setMyOrder($this);
		}

		return $this;
	}

	public function removeOrderDetail(OrderDetails $orderDetail): self
	{
		if ($this->orderDetails->removeElement($orderDetail)) {
			// set the owning side to null (unless already changed)
			if ($orderDetail->getMyOrder() === $this) {
				$orderDetail->setMyOrder(null);
			}
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getMarking()
	{
		return $this->marking;
	}

	/**
	 * @param mixed $marking
	 * @return Order
	 */
	public function setMarking($marking): self
	{
		$this->marking = $marking;
		return $this;
	}

	public function getTotal()
	{
		$total = null;
		foreach ($this->orderDetails->getValues() as $product) {
			$total += $product->getPrice() * $product->getQuantity();
		}
		return $total;
	}

	public function getStripeSessionId(): ?string
	{
		return $this->stripeSessionId;
	}

	public function setStripeSessionId(?string $stripeSessionId): self
	{
		$this->stripeSessionId = $stripeSessionId;

		return $this;
	}

	public function getReference(): ?string
	{
		return $this->reference;
	}

	public function setReference(string $reference): self
	{
		$this->reference = $reference;

		return $this;
	}

    public function getShippingFirstName(): ?string
    {
        return $this->shippingFirstName;
    }

    public function setShippingFirstName(string $shippingFirstName): self
    {
        $this->shippingFirstName = $shippingFirstName;

        return $this;
    }

    public function getShippingLastName(): ?string
    {
        return $this->shippingLastName;
    }

    public function setShippingLastName(string $shippingLastName): self
    {
        $this->shippingLastName = $shippingLastName;

        return $this;
    }

    public function getShippingAddress(): ?string
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(string $shippingAddress): self
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    public function getShippingCity(): ?string
    {
        return $this->shippingCity;
    }

    public function setShippingCity(string $shippingCity): self
    {
        $this->shippingCity = $shippingCity;

        return $this;
    }

    public function getShippingProvince(): ?string
    {
        return $this->shippingProvince;
    }

    public function setShippingProvince(string $shippingProvince): self
    {
        $this->shippingProvince = $shippingProvince;

        return $this;
    }

    public function getShippingPostalCode(): ?string
    {
        return $this->shippingPostalCode;
    }

    public function setShippingPostalCode(string $shippingPostalCode): self
    {
        $this->shippingPostalCode = $shippingPostalCode;

        return $this;
    }

    public function getShippingLat(): ?float
    {
        return $this->shippingLat;
    }

    public function setShippingLat(float $shippingLat): self
    {
        $this->shippingLat = $shippingLat;

        return $this;
    }

    public function getShippingLng(): ?float
    {
        return $this->shippingLng;
    }

    public function setShippingLng(float $shippingLng): self
    {
        $this->shippingLng = $shippingLng;

        return $this;
    }

    public function getShippingEmail(): ?string
    {
        return $this->shippingEmail;
    }

    public function setShippingEmail(string $shippingEmail): self
    {
        $this->shippingEmail = $shippingEmail;

        return $this;
    }

    public function getShippingPhone(): ?string
    {
        return $this->shippingPhone;
    }

    public function setShippingPhone(string $shippingPhone): self
    {
        $this->shippingPhone = $shippingPhone;

        return $this;
    }

    public function getStatus(): ?string
    {
        $status = '';
        switch ($this->marking){
            case 'waiting':
                $status = 'waiting';
                break;
            case 'in_payment':
                $status = 'in_payment';
                break;
            case 'paid':
                $status = 'paid';
                break;
            case 'checkout_canceled':
                $status = 'canceled';
                break;
            case 'in_preparation':
                $status = 'processing';
                break;
            case 'in_delivering':
                $status = 'shipped';
                break;
            case 'delivered':
                $status = 'delivered';
                break;
            case 'canceled':
                $status = 'canceled';

        }
        return $status;
    }


}
