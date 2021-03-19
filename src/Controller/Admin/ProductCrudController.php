<?php

namespace App\Controller\Admin;

use App\Admin\Field\TagField;
use App\Entity\Product;
use App\Form\CatalogType;
use App\Form\ImageFileType;
use App\Form\TagType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name'),
            SlugField::new('slug')->setTargetFieldName('name'),
            MoneyField::new('price')->setCurrency('KWD'),
            TextareaField::new('description'),
            TagField::new('tags', TagType::class)->onlyOnForms(),
            AssociationField::new('category'),
            CollectionField::new('images')
                ->setEntryType(ImageFileType::class)
                ->onlyOnForms(),
            CollectionField::new('catalogs')
                ->setEntryType(CatalogType::class)
                ->onlyOnForms(),
            TextEditorField::new('longDescription')->onlyOnForms(),
            NumberField::new('weight')->onlyOnForms(),
            TextField::new('materials')->onlyOnForms(),
        ];
    }

}
