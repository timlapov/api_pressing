<?php

namespace App\DataFixtures;

use App\Entity\ServiceCoefficients;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\Subcategory;
use App\Entity\Fabric;
use App\Entity\Service;
use App\Entity\Gender;
use App\Entity\Country;
use App\Entity\City;
use App\Entity\Client;
use App\Entity\Employee;
use App\Entity\OrderStatus;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Categories
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
                $subcategory->setImageUrl('placeholder.webp');
                $manager->persist($subcategory);
            }
        }

        // Fabrics
        $fabrics = ['Coton', 'Laine', 'Soie', 'Lin', 'Cuir', 'Synthétique'];
        foreach ($fabrics as $fabricName) {
            $fabric = new Fabric();
            $fabric->setName($fabricName);
            $fabric->setDescription("Description for $fabricName");
            $fabric->setPriceCoefficient(rand(10, 30) / 10);
            $manager->persist($fabric);
        }

        // Services
        $services = ['Lavage', 'Nettoyage à sec', 'Repassage', 'Détachage'];
        foreach ($services as $serviceName) {
            $service = new Service();
            $service->setName($serviceName);
            $service->setDescription("Description for $serviceName");
            $service->setPrice(rand(500, 2000) / 100);
            $service->setImageUrl('placeholder.webp');
            $manager->persist($service);
        }

        // Genders
        $genders = ['Homme', 'Femme', 'Personne non binaire', 'Autre'];
        foreach ($genders as $genderName) {
            $gender = new Gender();
            $gender->setName($genderName);
            $manager->persist($gender);
        }

        // Country and cities
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

        // Clients
        for ($i = 1; $i <= 10; $i++) {
            $client = new Client();
            $client->setEmail("client$i@example.com");
            $client->setRoles(['ROLE_USER']);
            $client->setPassword('password'); // Password will be hashed by event listener
            $client->setName("FirstName$i");
            $client->setSurname("LastName$i");
            $client->setBirthdate(new \DateTime('1980-01-01'));
            $client->setAddress("$i Peace Street");
            $client->setCity($manager->getRepository(City::class)->findOneBy([]));
            $client->setGender($manager->getRepository(Gender::class)->findOneBy([]));
            $manager->persist($client);
        }

// Admin (inactive)
        $admin = new Employee();
        $admin->setEmail("admin@admin.ru");
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword('password'); // Password will be hashed by event listener
        $admin->setName("Admin");
        $admin->setSurname("Admin");
        $admin->setPhoneNumber("0600000000");
        $admin->setActive(false);
        $manager->persist($admin);

// Employees (active)
        for ($i = 1; $i <= 5; $i++) {
            $employee = new Employee();
            $employee->setEmail("employee$i@example.com");
            $employee->setRoles(['ROLE_EMPLOYEE']);
            $employee->setPassword('password'); // Password will be hashed by event listener
            $employee->setName("EmployeeFirstName$i");
            $employee->setSurname("EmployeeLastName$i");
            $employee->setPhoneNumber("060000000$i");
            $employee->setActive(true);
            $manager->persist($employee);
        }

        // Order statuses
        $orderStatuses = ['Créé', 'Payé', 'En attente', 'En traitement', 'Prêt', 'Livré'];
        foreach ($orderStatuses as $statusName) {
            $status = new OrderStatus();
            $status->setName($statusName);
            $manager->persist($status);
        }

        // Service coefficients
        $coefficients = new ServiceCoefficients();
        $coefficients->setExpressCoefficient(1.3);
        $coefficients->setIroningCoefficient(1.1);
        $coefficients->setPerfumingCoefficient(1.05);
        $manager->persist($coefficients);

        $manager->flush();
    }
}