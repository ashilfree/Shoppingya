<?php

namespace App\Controller\Admin;

use App\Admin\Field\TagField;
use App\Entity\Category;
use App\Form\SizeType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name'),
            TextField::new('nameAr', 'الاسم'),
            TagField::new('sizes', SizeType::class)->onlyOnForms(),
        ];
    }

}
