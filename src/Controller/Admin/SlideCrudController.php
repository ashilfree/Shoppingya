<?php

namespace App\Controller\Admin;

use App\Entity\Slide;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class SlideCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Slide::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title'),
            TextField::new('titleAr', 'العنوان'),
            TextField::new('content')->onlyOnForms(),
            TextField::new('contentAr', 'المحتوى')->onlyOnForms(),
            TextField::new('btnTitle')->onlyOnForms(),
            TextField::new('btnTitleAr', 'عنوان الزر')->setTextAlign('right')->onlyOnForms(),
            UrlField::new('btnUrl')->onlyOnForms(),
            ImageField::new('imageFile')->setFormType(VichImageType::class)->onlyOnForms(),
            ImageField::new('fileName')->setCustomOption('basePath', 'media/images/slide/')->onlyOnIndex(),
        ];
    }

}
