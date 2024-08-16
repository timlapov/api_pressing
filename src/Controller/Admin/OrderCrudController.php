<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\Item;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield AssociationField::new('orderStatus')
            ->setFormTypeOption('choice_label', 'name')
            ->formatValue(function ($value, $entity) {
                return $entity->getOrderStatus() ? $entity->getOrderStatus()->getName() : '';
            });

        yield AssociationField::new('client')
            ->setFormTypeOption('choice_label', 'name')
            ->formatValue(function ($entity) {
                return substr($entity->getName(), 0, 1) . '. ' . $entity->getSurname();
            })
            ->setDisabled();

        yield AssociationField::new('employee')
            ->setFormTypeOption('choice_label', 'name')
            ->formatValue(function ($entity) {
                return substr($entity->getName(), 0, 1) . '. ' . $entity->getSurname();
            });

        yield DateTimeField::new('created')
            ->setFormat('dd/MM/yyyy HH:mm')
            ->setTimezone('Europe/Paris')
            ->setDisabled();

        yield DateTimeField::new('completed')
            ->setFormat('dd/MM/yyyy HH:mm')
            ->setTimezone('Europe/Paris')
            ->setDisabled();

        yield BooleanField::new('express')
            ->setLabel('Express');

        if (Crud::PAGE_DETAIL === $pageName || Crud::PAGE_INDEX === $pageName) {
            yield MoneyField::new('totalPrice')
                ->setCurrency('EUR')
                ->setCustomOption('storedAsCents', false);
        }

        if (Crud::PAGE_DETAIL === $pageName || Crud::PAGE_INDEX === $pageName) {
            yield CollectionField::new('items')
                ->setTemplatePath('admin/order/items.html.twig')
                ->onlyOnDetail();
        }
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['created' => 'DESC'])
            ->setSearchFields(['client.name', 'client.surname', 'employee.name', 'employee.surname', 'orderStatus.name'])
            ->setEntityLabelInSingular('Order')
            ->setEntityLabelInPlural('Orders');
    }
}