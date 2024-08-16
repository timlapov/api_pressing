<?php

namespace App\Controller\Admin;

use App\Entity\Item;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class ItemCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Item::class;
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

        yield AssociationField::new('subcategory')
            ->setFormTypeOption('choice_label', 'name')
            ->formatValue(function ($value, $entity) {
                return $entity->getSubcategory() ? $entity->getSubcategory()->getName() : '';
            });

        yield AssociationField::new('fabric')
            ->setFormTypeOption('choice_label', 'name')
            ->formatValue(function ($value, $entity) {
                return $entity->getFabric() ? $entity->getFabric()->getName() : '';
            });

        yield AssociationField::new('service')
            ->setFormTypeOption('choice_label', 'name')
            ->formatValue(function ($value, $entity) {
                return $entity->getService() ? $entity->getService()->getName() : '';
            })
            ->setDisabled(Crud::PAGE_EDIT !== $pageName);

        yield AssociationField::new('order_')
            ->setLabel('Order')
            ->setFormTypeOption('choice_label', function ($order) {
                return $order ? 'Order #' . $order->getId() : 'None';
            })
            ->formatValue(function ($value, $entity) {
                return $entity->getOrder() ? 'Order #' . $entity->getOrder()->getId() : 'None';
            })
            ->setDisabled();

        yield BooleanField::new('delicate')
            ->setLabel('Délicatement');

        yield BooleanField::new('perfuming')
            ->setLabel('Parfumage');

        yield MoneyField::new('calculatedPrice')
            ->setCurrency('EUR')
            ->setCustomOption('storedAsCents', false)
            ->setDisabled();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['subcategory.name', 'fabric.name', 'service.name', 'additionalService.name', 'order_.id'])
            ->setEntityLabelInSingular('Item')
            ->setEntityLabelInPlural('Items');
    }
}