<?php

namespace App\Form\DataTransformer;

use App\Entity\Tog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class StringToArrayTransformerTagsAr implements DataTransformerInterface{

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

        $togs = $this->entityManager->getRepository(Tog::class)->findBy([
            'name' => $names,
        ]);

        $newNames = array_diff($names, $togs);

        foreach ($newNames as $name) {
            $tog = new Tog();
            $tog->setName($name);
            $togs[] = $tog;
        }

        return $togs;
    }
}