<?php

namespace App\Controller\Admin;

use App\Entity\Catalog;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CatalogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Catalog::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
