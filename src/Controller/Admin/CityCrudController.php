<?php

namespace App\Controller\Admin;

use App\Entity\City;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

class CityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return City::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name');
        yield AssociationField::new('country')
            ->setFormTypeOption('choice_label', 'name')
            ->formatValue(function ($value, $entity) {
                return $entity->getCountry()->getName() ? $entity->getCountry()->getName() : '';
            });;

        if ($pageName === Crud::PAGE_DETAIL) {
            yield AssociationField::new('clients')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ]);
        }
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('City')
            ->setEntityLabelInPlural('Cities')
            ->setSearchFields(['name', 'country.name'])
            ->setDefaultSort(['name' => 'ASC']);
    }
}