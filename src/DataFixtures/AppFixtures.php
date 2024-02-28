<?php

namespace App\DataFixtures;

use App\Entity\Baie;
use App\Entity\Client;
use App\Entity\Forfait;
use App\Entity\Unite;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    public function load(ObjectManager $manager): void
    {
        // Création de données de test
        $data = [
            [
                'name' => 'Forfait Standard',
                'price' => 1500,
                'nbSlot' => 1,
                'discount' => 0,
                'nbMonth' => 1,
                'imgPath' => 'img/bronze.png'
            ],
            [
                'name' => 'Forfait Premium',
                'price' => 81000,
                'nbSlot' => 10,
                'discount' => 10,
                'nbMonth' => 6,
                'imgPath' => 'img/silver.png'
            ],
            [
                'name' => 'Forfait Premium +',
                'price' => 151200,
                'nbSlot' => 21,
                'discount' => 20,
                'nbMonth' => 6,
                'imgPath' => 'img/gold.png'
            ],
            [
                'name' => 'Forfait Premium ++',
                'price' => 264600,
                'nbSlot' => 21,
                'discount' => 30,
                'nbMonth' => 12,
                'imgPath' => 'img/platine.png'
            ]
            // Ajoutez d'autres données ici
        ];

        // Boucle pour créer et persister les entités Forfait
        foreach ($data as $item) {
            $forfait = new Forfait();
            $forfait->setName($item['name']);
            $forfait->setPrice($item['price']);
            $forfait->setNbSlot($item['nbSlot']);
            $forfait->setDiscount($item['discount']);
            $forfait->setNbMonth($item['nbMonth']);
            $forfait->setImgPath($item['imgPath']);

            $manager->persist($forfait);
        }

        $manager->flush();

        // Création de données de test pour les utilisateurs
        $userData = [
            [
                'email' => 'admin@example.com',
                'password' => 'admin_password',
                'roles' => ['ROLE_ADMIN'],
            ],
            [
                'email' => 'comptable@example.com',
                'password' => 'comptable_password',
                'roles' => ['ROLE_COMPTABLE'],
            ],
            [
                'email' => 'client@example.com',
                'password' => 'client_password',
                'roles' => ['ROLE_CLIENT'],
                'lastName' => 'NomClient',
                'firstName' => 'PrénomClient',
            ],
            // Ajoutez d'autres utilisateurs ici
        ];

        foreach ($userData as $data) {
            if ($data['roles']=='ROLE_CLIENT'){
                $user = new Client();
                $user->setLastName($data['lastName']);
                $user->setFirstName($data['firstName']);
            }else{
                $user = new User();
            }
            $user->setEmail($data['email']);
            $user->setPassword($this->hasher->hashPassword($user, $data['password']));
            $user->setRoles($data['roles']);

            $manager->persist($user);
        }

        $manager->flush();

        $baieData = [
            [
                'nbSpot' => 42,
                'code' => 'B001',
            ],
            [
                'nbSpot' => 42,
                'code' => 'B002',
            ],
            [
                'nbSpot' => 42,
                'code' => 'B003',
            ],
            [
                'nbSpot' => 42,
                'code' => 'B004',
            ],
            [
                'nbSpot' => 42,
                'code' => 'B005',
            ],
            [
                'nbSpot' => 42,
                'code' => 'B006',
            ],
            [
                'nbSpot' => 42,
                'code' => 'B007',
            ],
            // Ajoutez d'autres données ici
        ];

        foreach ($baieData as $data) {
            $baie = new Baie();
            $baie->setNbSpot($data['nbSpot']);
            $baie->setCode($data['code']);

            $manager->persist($baie);
        }

        $manager->flush();

        $baieRepository = $manager->getRepository(Baie::class);
        $baies = $baieRepository->findAll();

        foreach ($baies as $baie) {
            for ($i = 1; $i <= $baie->getNbSpot(); $i++) {
                $unite = new Unite();
                $unite->setNumSpot($i);
                $unite->setAvailable(true);
                $unite->setBaie($baie);
                $manager->persist($unite);
            }
        }

        $manager->flush();
    }
}
