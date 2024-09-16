<?php

namespace App\DataFixtures;

use App\Entity\ServiceCoefficient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\Subcategory;
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
            'Chemises et chemisiers' => [
                'Chemise' => 1.1,
                'Chemisier' => 2.1,
                'Polo' => 1.5,
                'T-shirt' => 1.8
            ],
            'Pantalons et jupes' => [
                'Pantalon' => 2.3,
                'Jupe' => 2.3,
                'Short' => 2.1
            ],
            'Costumes' => [
                'Costume 2 pièces' => 4.9,
                'Costume 3 pièces' => 6.4
            ],
            'Robes' => [
                'Robe simple' => 3.8,
                'Robe de soirée' => 6.4,
                'Combinaison' => 4.9
            ],
            'Vêtements d\'extérieur' => [
                'Veste' => 3.3,
                'Manteau' => 5.6,
                'Trench' => 6.6,
                'Doudoune' => 7.7
            ],
            'Accessoires' => [
                'Foulard' => 2.1,
                'Cravate' => 2.1,
                'Chaussettes' => 0.8
            ],
            'Linge de maison' => [
                'Drap simple' => 1.3,
                'Drap double' => 1.8,
                'Housse de couette simple' => 2.1,
                'Housse de couette double' => 2.8,
                'Taie d\'oreiller' => 1.0
            ],
            'Articles en cuir' => [
                'Chaussures en cuir' => 7.0,
                'Bottes en cuir' => 8.0,
                'Ceinture en cuir' => 4.0,
                'Sac en cuir' => 9.0
            ]
        ];

        foreach ($categories as $categoryName => $subcategories) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);

            foreach ($subcategories as $subcategoryName => $priceCoefficient) {
                $subcategory = new Subcategory();
                $subcategory->setName($subcategoryName);
                $subcategory->setCategory($category);
                $subcategory->setPriceCoefficient($priceCoefficient);
                $subcategory->setImageUrl('placeholder.webp');
                $manager->persist($subcategory);
            }
        }

        // Services
        $services = [
            'Lavage' => [
                'description' => 'Pour le linge de tous les jours, les draps et les serviettes.',
                'price' => 1.9
            ],
            'Nettoyage à sec' => [
                'description' => 'Il s\'agit d\'un traitement de nettoyage professionnel utilisant des solvants pour éliminer les taches et la saleté des tissus délicats.',
                'price' => 3.9
            ],
            'Blanchiment' => [
                'description' => 'Pour éliminer les taches tenaces et retrouver leur blancheur d\'origine.',
                'price' => 2.9
            ],
            'Traitement anti-taches' => [
                'description' => 'Pour protéger les vêtements contre les taches futures.',
                'price' => 2.9
            ]
        ];

        foreach ($services as $serviceName => $serviceData) {
            $service = new Service();
            $service->setName($serviceName);
            $service->setDescription($serviceData['description']);
            $service->setPrice($serviceData['price']);
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

        $cities = [
            'Lyon 69001', 'Lyon 69002', 'Lyon 69003', 'Lyon 69004', 'Lyon 69005', 'Lyon 69006', 'Lyon 69007',
            'Lyon 69008', 'Lyon 69009', 'Villeurbanne 69100'
        ];
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
        $coefficients = new ServiceCoefficient();
        $coefficients->setExpressCoefficient(1.3);
        $coefficients->setIroningCoefficient(1.1);
        $coefficients->setPerfumingCoefficient(1.00);
        $manager->persist($coefficients);

        $manager->flush();
    }
}