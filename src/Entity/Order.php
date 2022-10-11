<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\JoinColumn(nullable=true)
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
    private $invoiceId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $invoiceKey;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $reference;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $shippingFullName;

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
    private $shippingEmail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max="8")
     */
    private $shippingPhone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $paymentMethod;

    /**
     * @ORM\Column(type="float")
     */
    private $total;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $paid_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $ordered_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $inDelivering_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $delivered_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $cancelled_at;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPaid;


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

    public function getTotalOrder()
    {
        return ($this->getTotal() + $this->getDeliveryPrice());
    }

    public function getInvoiceId(): ?string
    {
        return $this->invoiceId;
    }

    public function setInvoiceId(?string $invoiceId): self
    {
        $this->invoiceId = $invoiceId;

        return $this;
    }

    public function getInvoiceKey(): ?string
    {
        return $this->invoiceKey;
    }

    public function setInvoiceKey(?string $invoiceKey): self
    {
        $this->invoiceKey = $invoiceKey;

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

    public function getShippingFullName(): ?string
    {
        return $this->shippingFullName;
    }

    public function setShippingFullName(string $shippingFullName): self
    {
        $this->shippingFullName = $shippingFullName;

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
        switch ($this->marking) {
            case 'waiting':
            case 'in_payment':
                $status = 'waiting';
                break;
            case 'paid':
            case 'pay_en_delivery':
                $status = 'Processing';
                break;
            case 'checkout_canceled':
            case 'canceled':
                $status = 'canceled';
                break;
            case 'in_delivering':
                $status = 'In delivering';
                break;
            case 'delivered':
                $status = 'Delivered';
                break;
        }
        return $status;
    }

    public function canChange(): ?string
    {
        $status = '';
        switch ($this->marking) {
            case 'waiting':
            case 'in_payment':
            case 'checkout_canceled':
            case 'canceled':
            case 'delivered':
                $status = '';
                break;
            case 'paid':
            case 'pay_en_delivery':
                $status = 'In Delivering';
                break;
            case 'in_delivering':
                $status = 'Delivered';
                break;
        }
        return $status;
    }

    public function canCancelled(): string
    {
        $status = '';
        if($this->marking == 'pay_en_delivery')
            $status = 'Canceled';
        return $status;
    }

    public function canPrint(): string
    {
        $status = '';
        if($this->marking == 'in_delivering' || $this->marking == 'delivered')
            $status = 'Delivery Invoice';
        return $status;
    }

    public function getStatusAr(): ?string
    {
        $status = '';
        switch ($this->marking) {
            case 'waiting':
            case 'in_payment':
                $status = 'الانتظار';
                break;
            case 'paid':
            case 'pay_en_delivery':
                $status = 'الطلب تحت المعالجة';
                break;
            case 'checkout_canceled':
            case 'canceled':
                $status = 'ملغى';
                break;
            case 'in_delivering':
                $status = 'في طريق التسليم';
                break;
            case 'delivered':
                $status = 'تم التسليم';
                break;
        }
        return $status;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getPaidAt(): ?\DateTimeInterface
    {
        return $this->paid_at;
    }

    public function setPaidAt(?\DateTimeInterface $paid_at): self
    {
        $this->paid_at = $paid_at;

        return $this;
    }

    public function getOrderedAt(): ?\DateTimeInterface
    {
        return $this->ordered_at;
    }

    public function setOrderedAt(?\DateTimeInterface $ordered_at): self
    {
        $this->ordered_at = $ordered_at;

        return $this;
    }

    public function getInDeliveringAt(): ?\DateTimeInterface
    {
        return $this->inDelivering_at;
    }

    public function setInDeliveringAt(?\DateTimeInterface $inDelivering_at): self
    {
        $this->inDelivering_at = $inDelivering_at;

        return $this;
    }

    public function getDeliveredAt(): ?\DateTimeInterface
    {
        return $this->delivered_at;
    }

    public function setDeliveredAt(?\DateTimeInterface $delivered_at): self
    {
        $this->delivered_at = $delivered_at;

        return $this;
    }

    public function getCancelledAt(): ?\DateTimeInterface
    {
        return $this->cancelled_at;
    }

    public function setCancelledAt(?\DateTimeInterface $cancelled_at): self
    {
        $this->cancelled_at = $cancelled_at;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param mixed $paymentMethod
     */
    public function setPaymentMethod($paymentMethod): void
    {
        $this->paymentMethod = $paymentMethod;
    }

    public function getIsPaid(): bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): self
    {
        $this->isPaid = $isPaid;

        return $this;
    }
}
