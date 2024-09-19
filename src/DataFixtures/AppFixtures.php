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
        // Вownload from file data.json
        $data = json_decode(file_get_contents(__DIR__ . '/data.json'), true);

        // Categories and Subcategories
        foreach ($data['categories'] as $categoryData) {
            $category = new Category();
            $category->setName($categoryData['name']);
            $manager->persist($category);

            foreach ($categoryData['subcategories'] as $subcategoryData) {
                $subcategory = new Subcategory();
                $subcategory->setName($subcategoryData['name']);
                $subcategory->setCategory($category);
                $subcategory->setPriceCoefficient($subcategoryData['priceCoefficient']);
                $subcategory->setImageUrl($subcategoryData['imageUrl']);
                $manager->persist($subcategory);
            }
        }

        // Services
        foreach ($data['services'] as $serviceData) {
            $service = new Service();
            $service->setName($serviceData['name']);
            $service->setDescription($serviceData['description']);
            $service->setPrice($serviceData['price']);
            $service->setImageUrl($serviceData['imageUrl']);
            $manager->persist($service);
        }

        // Genders
        foreach ($data['genders'] as $genderName) {
            $gender = new Gender();
            $gender->setName($genderName);
            $manager->persist($gender);
        }

        // Country and cities
        $countryData = $data['country'];
        $country = new Country();
        $country->setName($countryData['name']);
        $manager->persist($country);

        foreach ($countryData['cities'] as $cityName) {
            $city = new City();
            $city->setName($cityName);
            $city->setCountry($country);
            $manager->persist($city);
        }

        // Order statuses
        foreach ($data['orderStatuses'] as $statusData) {
            $status = new OrderStatus();
            $status->setName($statusData['name']);
            $status->setDescription($statusData['description']);
            $manager->persist($status);
        }

        // Service coefficients
        $coefficientsData = $data['serviceCoefficients'];
        $coefficients = new ServiceCoefficient();
        $coefficients->setExpressCoefficient($coefficientsData['expressCoefficient']);
        $coefficients->setIroningCoefficient($coefficientsData['ironingCoefficient']);
        $coefficients->setPerfumingCoefficient($coefficientsData['perfumingCoefficient']);
        $manager->persist($coefficients);

        // Lists of first and last names for random selection
        $firstNames = $data['firstNames'];
        $lastNames = $data['lastNames'];

        // Getting repositories for randomly selecting city and gender
        $cityRepository = $manager->getRepository(City::class);
        $genderRepository = $manager->getRepository(Gender::class);

        // Clients
        for ($i = 1; $i <= 10; $i++) {
            $client = new Client();
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $client->setEmail("client$i@example.com");
            $client->setRoles(['ROLE_USER']);
            $client->setPassword('password'); // The password will be hashed by the event listener
            $client->setName($firstName);
            $client->setSurname($lastName);
            $client->setBirthdate(new \DateTime('1980-01-01'));
            $client->setAddress("$i Peace Street");
            $client->setCity($cityRepository->findOneBy([], ['id' => 'ASC']));
            $client->setGender($genderRepository->findOneBy([], ['id' => 'ASC']));
            $manager->persist($client);
        }

        // Admin (inactive)
        $admin = new Employee();
        $admin->setEmail("admin@propre-propre.fr");
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword('admin'); // The password will be hashed by the event listener
        $admin->setName("Bernard");
        $admin->setSurname("Magnenat");
        $admin->setPhoneNumber("0600000000");
        $admin->setActive(false);
        $manager->persist($admin);

        // Employees (active)
        for ($i = 1; $i <= 5; $i++) {
            $employee = new Employee();
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $email = strtolower($firstName[0] . '.' . $lastName . $i . '@propre-propre.fr');
            $employee->setEmail($email);
            $employee->setRoles(['ROLE_EMPLOYEE']);
            $employee->setPassword('password'); // The password will be hashed by the event listener
            $employee->setName($firstName);
            $employee->setSurname($lastName);
            $employee->setPhoneNumber("060000000$i");
            $employee->setActive(true);
            $manager->persist($employee);
        }

        $manager->flush();
    }
}