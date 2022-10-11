<?php

namespace App\Controller\Admin;

use App\Entity\Governorate;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GovernorateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Governorate::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->overrideTemplate('crud/new', 'admin/governorate/new.html.twig')
            ->overrideTemplate('crud/edit', 'admin/governorate/edit.html.twig')
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name'),
            TextField::new('nameAr', 'الاسم')->addCssClass('text-right'),
            MoneyField::new('price')->setCurrency('KWD'),
        ];
    }

}
