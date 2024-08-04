<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\Subcategory;
use App\Entity\Fabric;
use App\Entity\Service;
use App\Entity\AdditionalService;
use App\Entity\Gender;
use App\Entity\Country;
use App\Entity\City;
use App\Entity\Client;
use App\Entity\Employee;
use App\Entity\OrderStatus;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Категории
        $categories = [
            'Vêtements' => ['Chemise', 'Pantalon', 'Robe', 'Manteau'],
            'Linge de maison' => ['Draps', 'Serviettes', 'Rideaux'],
            'Accessoires' => ['Sac', 'Chaussures', 'Ceinture']
        ];

        foreach ($categories as $categoryName => $subcategoryNames) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);

            foreach ($subcategoryNames as $subcategoryName) {
                $subcategory = new Subcategory();
                $subcategory->setName($subcategoryName);
                $subcategory->setCategory($category);
                $subcategory->setPriceCoefficient(rand(10, 20) / 10);
                $manager->persist($subcategory);
            }
        }

        // Ткани
        $fabrics = ['Coton', 'Laine', 'Soie', 'Lin', 'Cuir', 'Synthétique'];
        foreach ($fabrics as $fabricName) {
            $fabric = new Fabric();
            $fabric->setName($fabricName);
            $fabric->setDescription("Description pour $fabricName");
            $fabric->setPriceCoefficient(rand(10, 30) / 10);
            $manager->persist($fabric);
        }

        // Услуги
        $services = ['Lavage', 'Nettoyage à sec', 'Repassage', 'Détachage'];
        foreach ($services as $serviceName) {
            $service = new Service();
            $service->setName($serviceName);
            $service->setDescription("Description pour $serviceName");
            $service->setPrice(rand(500, 2000) / 100);
            $manager->persist($service);
        }

        // Дополнительные услуги
        $additionalServices = ['Express', 'Parfumage', 'Imperméabilisation'];
        foreach ($additionalServices as $additionalServiceName) {
            $additionalService = new AdditionalService();
            $additionalService->setName($additionalServiceName);
            $additionalService->setPriceCoefficient(rand(110, 150) / 100);
            $manager->persist($additionalService);
        }

        // Пол
        $genders = ['Homme', 'Femme', 'Autre'];
        foreach ($genders as $genderName) {
            $gender = new Gender();
            $gender->setName($genderName);
            $manager->persist($gender);
        }

        // Страна и город
        $country = new Country();
        $country->setName('France');
        $manager->persist($country);

        $cities = ['Paris', 'Lyon', 'Marseille', 'Bordeaux', 'Lille'];
        foreach ($cities as $cityName) {
            $city = new City();
            $city->setName($cityName);
            $city->setCountry($country);
            $manager->persist($city);
        }

        // Клиенты
        for ($i = 1; $i <= 10; $i++) {
            $client = new Client();
            $client->setEmail("client$i@example.com");
            $client->setRoles(['ROLE_CLIENT']);
            $client->setPassword($this->passwordHasher->hashPassword($client, 'password'));
            $client->setName("Prénom$i");
            $client->setSurname("Nom$i");
            $client->setBirthdate(new \DateTime('1980-01-01'));
            $client->setAddress("$i Rue de la Paix");
            $client->setCity($manager->getRepository(City::class)->findOneBy([]));
            $client->setGender($manager->getRepository(Gender::class)->findOneBy([]));
            $manager->persist($client);
        }

        // Сотрудники
        for ($i = 1; $i <= 5; $i++) {
            $employee = new Employee();
            $employee->setEmail("employee$i@example.com");
            $employee->setRoles(['ROLE_EMPLOYEE']);
            $employee->setPassword($this->passwordHasher->hashPassword($employee, 'password'));
            $employee->setName("EmployéPrénom$i");
            $employee->setSurname("EmployéNom$i");
            $employee->setPhoneNumber("060000000$i");
            $manager->persist($employee);
        }

        // Статусы заказов
        $orderStatuses = ['Créé', 'Payé', 'En attente', 'En traitement', 'Prêt', 'Livré'];
        foreach ($orderStatuses as $statusName) {
            $status = new OrderStatus();
            $status->setName($statusName);
            $manager->persist($status);
        }

        $manager->flush();
    }
}