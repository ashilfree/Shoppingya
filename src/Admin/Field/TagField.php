<?php


namespace App\Admin\Field;


use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

class TagField implements FieldInterface
{
    use FieldTrait;
    public static function new(string $propertyName, ?string $label = null): TagField
    {

        return (new self())
            ->setProperty($propertyName)
            ->setFormType($label)
            ->setFormTypeOptions(["attr"=>['data-role'=>'tagsinput']])
            ->addCssFiles('js/admin/bs-tags/bootstrap-tagsinput.css')
            ->addJsFiles('js/admin/bs-tags/bootstrap-tagsinput.js')
            ;
    }
}