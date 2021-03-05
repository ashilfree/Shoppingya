<?php

namespace App\Form\DataTransformer;

use App\Entity\Size;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class StringToArrayTransformerSizes implements DataTransformerInterface{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function transform($value)
    {
        return implode(', ', $value);
    }


    public function reverseTransform($value)
    {
        $names = array_unique(array_filter(array_map('trim', explode(',', $value))));

        $sizes = $this->entityManager->getRepository(Size::class)->findBy([
            'name' => $names,
        ]);

        $newNames = array_diff($names, $sizes);

        foreach ($newNames as $name) {
            $size = new Size();
            $size->setName($name);
            $sizes[] = $size;
        }

        return $sizes;
    }
}