<?php

namespace App\Controller\Admin;

use App\Classes\MYPDF;
use App\Classes\Transaction;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
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
            ->overrideTemplate('crud/detail', 'admin/order/detail.html.twig')
            ->overrideTemplate('crud/action', 'admin/order/action.html.twig')
            ->setPaginatorPageSize(1000)
            ;
    }
    public function configureActions(Actions $actions): Actions
    {
        $inDelivering = Action::new('inDelivering', 'In Delivering')->linkToCrudAction('inDelivering');
        $delivered = Action::new('delivered', 'Delivered')->linkToCrudAction('delivered');
        $canceled = Action::new('canceled', 'Canceled')->linkToCrudAction('canceled');
        $pdf = Action::new('invoice', 'Delivery Invoice')->linkToCrudAction('invoice');
        return $actions
//            ->add('index', $subCategory)
//            ->update(Crud::PAGE_INDEX, $subCategory, function (Action $action) {
//                return $action->setIcon('fa fa-truck');
//            })*
            ->add(Crud::PAGE_INDEX, $canceled)
            ->add(Crud::PAGE_INDEX, $delivered)
            ->add(Crud::PAGE_INDEX, $inDelivering)
            ->add(Crud::PAGE_INDEX, $pdf)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ;
    }

    public function inDelivering(AdminContext $context): RedirectResponse
    {
        /**
         * @var Order $entity
         */
        $entity = $context->getEntity()->getInstance();
        $url = $this->crudUrlGenerator->build()
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();
        if ($this->transaction->check($entity, 'in_delivering'))
            $this->transaction->applyWorkFlow($entity, 'in_delivering');
        if ($this->transaction->check($entity, 'in_delivering2')){
            $this->transaction->applyWorkFlow($entity, 'in_delivering2');
        }

            $entity->setInDeliveringAt(new \DateTime());

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
            $entity->setDeliveredAt(new \DateTime());
            if($entity->getPaymentMethod() == "PAY EN DELIVERY")
                $entity->setIsPaid(1);
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
            $entity->setCancelledAt(new \DateTime());
        }
        if ($this->transaction->check($entity, 'order_canceled2')) {
            $this->transaction->applyWorkFlow($entity, 'order_canceled2');
            $entity->setCancelledAt(new \DateTime());
        }
        $this->entityManager->flush();
        return $this->redirect($url);
    }

    public function invoice(AdminContext $context)
    {
       $entity = $context->getEntity()->getInstance();
       $html = $this->renderView('order/invoice2.html.twig', [
           'order' => $entity
       ]);
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->SetHeaderData('', PDF_HEADER_LOGO_WIDTH, '', '', array(
            0,
            0,
            0
        ), array(
            255,
            255,
            255
        ));
        $pdf->SetFont('aealarabiya', '', 18);
        $pdf->SetTitle('Invoice - ' . $entity->getReference());
        $pdf->SetMargins(20, 20, 20, true);
        $pdf->AddPage();

        $filename = "Invoice-" . $entity->getReference();
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output($filename . '.pdf', 'I');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('marking')
            ->add('isPaid')
            ->add('createdAt')
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),

            DateField::new('createdAt'),
            TextField::new('marking', 'Status'),
            TextField::new('shippingFullName', 'Full Name'),
            TelephoneField::new('shippingPhone', 'Phone'),
            BooleanField::new('isPaid')->renderAsSwitch(false),
            MoneyField::new('totalOrder')->setCurrency('KWD'),
        ];
    }

}
