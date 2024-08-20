<?php

namespace App\Controller\Admin;

use App\Entity\Employee;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

class EmployeeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Employee::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield EmailField::new('email')
            ->hideOnIndex();
        yield TextField::new('password')
            ->hideOnIndex();
        yield TextField::new('name')
            ->hideOnIndex();
        yield TextField::new('surname');
        yield TelephoneField::new('phoneNumber');
        yield BooleanField::new('active');
        yield ChoiceField::new('roles')
            ->setChoices([
                'Employee' => 'ROLE_EMPLOYEE',
                'Admin' => 'ROLE_ADMIN',
            ])
            ->allowMultipleChoices()
            ->setRequired(true);
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
            ->setEntityLabelInSingular('Employee')
            ->setEntityLabelInPlural('Employees')
            ->setSearchFields(['email', 'name', 'surname', 'phoneNumber'])
            ->setDefaultSort(['id' => 'DESC']);
    }
}