<?php

namespace App\Entity;

use App\Repository\AboutRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=AboutRepository::class)
 * @Vich\Uploadable()
 */
class About
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
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titleAr;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description1;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descriptionAr1;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description2;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descriptionAr2;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description3;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descriptionAr3;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $word;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $wordAr;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $word_honor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $word_honor_ar;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255)
     */
    private $fileName;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="about_images", fileNameProperty="fileName")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription1(): ?string
    {
        return $this->description1;
    }

    public function setDescription1(?string $description1): self
    {
        $this->description1 = $description1;

        return $this;
    }

    public function getDescription2(): ?string
    {
        return $this->description2;
    }

    public function setDescription2(?string $description2): self
    {
        $this->description2 = $description2;

        return $this;
    }

    public function getDescription3(): ?string
    {
        return $this->description3;
    }

    public function setDescription3(?string $description3): self
    {
        $this->description3 = $description3;

        return $this;
    }

    public function getWord(): ?string
    {
        return $this->word;
    }

    public function setWord(?string $word): self
    {
        $this->word = $word;

        return $this;
    }

    public function getWordHonor(): ?string
    {
        return $this->word_honor;
    }

    public function setWordHonor(?string $word_honor): self
    {
        $this->word_honor = $word_honor;

        return $this;
    }

    public function getTitleAr(): ?string
    {
        return $this->titleAr;
    }

    public function setTitleAr(string $titleAr): self
    {
        $this->titleAr = $titleAr;

        return $this;
    }

    public function getDescriptionAr1(): ?string
    {
        return $this->descriptionAr1;
    }

    public function setDescriptionAr1(?string $descriptionAr1): self
    {
        $this->descriptionAr1 = $descriptionAr1;

        return $this;
    }

    public function getDescriptionAr2(): ?string
    {
        return $this->descriptionAr2;
    }

    public function setDescriptionAr2(?string $descriptionAr2): self
    {
        $this->descriptionAr2 = $descriptionAr2;

        return $this;
    }

    public function getDescriptionAr3(): ?string
    {
        return $this->descriptionAr3;
    }

    public function setDescriptionAr3(?string $descriptionAr3): self
    {
        $this->descriptionAr3 = $descriptionAr3;

        return $this;
    }

    public function getWordAr(): ?string
    {
        return $this->wordAr;
    }

    public function setWordAr(?string $wordAr): self
    {
        $this->wordAr = $wordAr;

        return $this;
    }

    public function getWordHonorAr(): ?string
    {
        return $this->word_honor_ar;
    }

    public function setWordHonorAr(?string $word_honor_ar): self
    {
        $this->word_honor_ar = $word_honor_ar;

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
