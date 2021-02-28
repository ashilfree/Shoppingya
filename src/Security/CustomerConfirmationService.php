<?php

namespace App\Security;

use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CustomerConfirmationService{
    /**
     * @var CustomerRepository
     */
    private $customerRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    /**
     * CustomerConfirmationService constructor.
     * @param CustomerRepository $customerRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        CustomerRepository $customerRepository,
        EntityManagerInterface $entityManager
)
    {
        $this->customerRepository = $customerRepository;
        $this->entityManager = $entityManager;
    }

    public function confirmCustomer(string $confirmationToken)
    {
        $customer = $this->customerRepository->findOneBy([
            'confirmationToken' => $confirmationToken
        ]);

        // customer was found by confirmation token
        if(!$customer){
            throw new NotFoundHttpException();
        }
        $customer->setEnabled(true);
        $customer->setConfirmationToken(null);
        $this->entityManager->flush();

    }
}