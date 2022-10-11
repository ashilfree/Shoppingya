<?php

namespace App\Controller\Admin;

use App\Entity\About;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
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

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
//            ->overrideTemplate('crud/index', 'admin/slide/index.html.twig')
            ->overrideTemplate('crud/new', 'admin/about/new.html.twig')
            ->overrideTemplate('crud/edit', 'admin/about/edit.html.twig')
//            ->overrideTemplate('crud/field/image', 'admin/slide/field/image.html.twig')
            ->setFormThemes(['@EasyAdmin/crud/form_theme.html.twig','admin/about/form.html.twig'])
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title'),
            TextField::new('titleAr', 'العنوان')->addCssClass('text-right'),
            TextEditorField::new('description1', 'First description')->onlyOnForms(),
            TextEditorField::new('descriptionAr1', 'الوصف الاول')->onlyOnForms()->addCssClass('text-right'),
            TextEditorField::new('description2', 'Second description')->onlyOnForms(),
            TextEditorField::new('descriptionAr2', 'الوصف الثاني')->onlyOnForms()->addCssClass('text-right'),
            TextEditorField::new('description3', 'Third description')->onlyOnForms(),
            TextEditorField::new('descriptionAr3', 'الوصف الثالث')->onlyOnForms()->addCssClass('text-right'),
            TextEditorField::new('word')->onlyOnForms(),
            TextEditorField::new('wordAr', 'الكلمة')->onlyOnForms()->addCssClass('text-right'),
            TextField::new('word_honor')->onlyOnForms(),
            TextField::new('word_honor_ar', 'صاحب الكلمة')->onlyOnForms()->addCssClass('text-right'),
            ImageField::new('imageFile')->setFormType(VichImageType::class)->onlyOnForms(),
            ImageField::new('fileName')->setCustomOption('basePath', 'media/images/about/')->onlyOnIndex(),
        ];
    }

}
