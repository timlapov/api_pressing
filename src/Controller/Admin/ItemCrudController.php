<?php

namespace App\Controller\Admin;

use App\Entity\Item;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

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
            ->setFormTypeOption('choice_label', function ($subcategory) {
                return $subcategory->getName();
            });

        yield AssociationField::new('fabric')
            ->setFormTypeOption('choice_label', function ($fabric) {
                return $fabric->getName();
            });

        yield AssociationField::new('service')
            ->setFormTypeOption('choice_label', function ($service) {
                return $service->getName();
            })
            ->setDisabled(Crud::PAGE_EDIT !== $pageName);

        yield AssociationField::new('additionalService')
            ->setFormTypeOption('choice_label', function ($additionalService) {
                return $additionalService ? $additionalService->getName() : 'None';
            })
            ->setRequired(false);

        yield AssociationField::new('order_')
            ->setFormTypeOption('choice_label', function ($order) {
                return $order ? 'Order #' . $order->getId() : 'None';
            })
            ->setDisabled();

        yield MoneyField::new('calculatedPrice')
            ->setCurrency('EUR')
            ->setCustomOption('storedAsCents', false)
            ->setDisabled();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'DESC'])
            ->setEntityLabelInSingular('Item')
            ->setEntityLabelInPlural('Items');
    }
}
