<?php

namespace App\Controller\Admin;

use App\Classes\Transaction;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;

class OrderCrudController extends AbstractCrudController
{
    /**
     * @var CrudUrlGenerator
     */
    private $crudUrlGenerator;
    /**
     * @var AdminContextProvider
     */
    private $adminContextProvider;
    /**
     * @var Transaction
     */
    private $transaction;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(CrudUrlGenerator $crudUrlGenerator, AdminContextProvider $adminContextProvider, Transaction $transaction, EntityManagerInterface $entityManager)
    {
        $this->crudUrlGenerator = $crudUrlGenerator;
        $this->adminContextProvider = $adminContextProvider;
        $this->transaction = $transaction;
        $this->entityManager = $entityManager;
    }
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->overrideTemplate('crud/index', 'admin/order/index.html.twig')
            ->overrideTemplate('crud/action', 'admin/order/action.html.twig')
            ;
    }
    public function configureActions(Actions $actions): Actions
    {
        $inDelivering = Action::new('inDelivering', 'In Delivering')->linkToCrudAction('inDelivering');
        $delivered = Action::new('delivered', 'Delivered')->linkToCrudAction('delivered');
        $canceled = Action::new('canceled', 'Canceled')->linkToCrudAction('canceled');
        return $actions
//            ->add('index', $subCategory)
//            ->update(Crud::PAGE_INDEX, $subCategory, function (Action $action) {
//                return $action->setIcon('fa fa-truck');
//            })*
            ->add(Crud::PAGE_INDEX, $canceled)
            ->add(Crud::PAGE_INDEX, $delivered)
            ->add(Crud::PAGE_INDEX, $inDelivering)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ;
    }

    public function inDelivering(AdminContext $context): RedirectResponse
    {
        $entity = $context->getEntity()->getInstance();
        $url = $this->crudUrlGenerator->build()
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();
        if ($this->transaction->check($entity, 'in_delivering')) {
            $this->transaction->applyWorkFlow($entity, 'in_delivering');
        }
        $this->entityManager->flush();
        return $this->redirect($url);
    }

    public function delivered(AdminContext $context): RedirectResponse
    {
        $entity = $context->getEntity()->getInstance();
        $url = $this->crudUrlGenerator->build()
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();
        if ($this->transaction->check($entity, 'delivery_done')) {
            $this->transaction->applyWorkFlow($entity, 'delivery_done');
        }
        $this->entityManager->flush();
        return $this->redirect($url);
    }

    public function canceled(AdminContext $context): RedirectResponse
    {
        $entity = $context->getEntity()->getInstance();
        $url = $this->crudUrlGenerator->build()
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();
        if ($this->transaction->check($entity, 'order_canceled')) {
            $this->transaction->applyWorkFlow($entity, 'order_canceled');
        }
        if ($this->transaction->check($entity, 'order_canceled2')) {
            $this->transaction->applyWorkFlow($entity, 'order_canceled2');
        }
        $this->entityManager->flush();
        return $this->redirect($url);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            AssociationField::new('customer'),
            DateField::new('createdAt'),
            MoneyField::new('deliveryPrice')->setCurrency('KWD'),
            TextField::new('marking'),
            TextField::new('shippingAddress'),
            EmailField::new('shippingEmail'),
            TelephoneField::new('shippingPhone'),
            MoneyField::new('total')->setCurrency('KWD'),
        ];
    }

}
