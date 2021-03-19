<?php

namespace App\Controller\Admin;

use App\Entity\About;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AboutCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return About::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title'),
            TextEditorField::new('description1')->onlyOnForms(),
            TextEditorField::new('description2')->onlyOnForms(),
            TextEditorField::new('description3')->onlyOnForms(),
            TextEditorField::new('word')->onlyOnForms(),
            TextField::new('word_honor')->onlyOnForms(),
            ImageField::new('imageFile')->setFormType(VichImageType::class)->onlyOnForms(),
            ImageField::new('fileName')->setCustomOption('basePath', 'media/images/about/')->onlyOnIndex(),
        ];
    }

}
