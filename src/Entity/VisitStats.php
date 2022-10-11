<?php

namespace App\Entity;

use App\Repository\VisitStatsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VisitStatsRepository::class)
 */
class VisitStats
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ip;

    /**
     * @ORM\Column(type="datetime")
     */
    private $visitAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $visitNumber;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getVisitAt(): ?\DateTimeInterface
    {
        return $this->visitAt;
    }

    public function setVisitAt(\DateTimeInterface $visitAt): self
    {
        $this->visitAt = $visitAt;

        return $this;
    }

    public function getVisitNumber(): ?int
    {
        return $this->visitNumber;
    }

    public function setVisitNumber(int $visitNumber): self
    {
        $this->visitNumber = $visitNumber;

        return $this;
    }

}
