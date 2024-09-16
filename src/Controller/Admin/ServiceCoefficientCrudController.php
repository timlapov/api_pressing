<?php

namespace App\Controller\Admin;

use App\Entity\ServiceCoefficient;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ServiceCoefficientCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ServiceCoefficient::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::EDIT, Action::DELETE) // Отключаем редактирование и удаление
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_NEW, Action::INDEX); // Добавляем кнопку "Назад к списку" на странице создания
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            NumberField::new('expressCoefficient')->setLabel('Express Coefficient'),
            NumberField::new('ironingCoefficient')->setLabel('Ironing Coefficient'),
            NumberField::new('perfumingCoefficient')->setLabel('Perfuming Coefficient'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Service Coefficient')
            ->setEntityLabelInPlural('Service Coefficients')
            ->setPageTitle(Crud::PAGE_NEW, 'Add new Service Coefficient')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Service Coefficient Details');
    }
}
