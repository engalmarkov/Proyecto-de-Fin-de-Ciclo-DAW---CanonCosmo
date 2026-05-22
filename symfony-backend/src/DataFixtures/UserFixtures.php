<?php
namespace App\DataFixtures;

use App\Entity\Usuario;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        // Administrador
        $admin = new Usuario();
        $admin->setEmail('admin@frikistore.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, '123456'));
        $admin->setNombre('Admin'); 
        $admin->setApellidos('Canon Cosmo'); 
        
        $manager->persist($admin);

        // Usuario Normal
        $user = new Usuario();
        $user->setEmail('user@frikistore.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, '123456'));
        $user->setNombre('Angel');
        $user->setApellidos('Espinosa');
        
        $manager->persist($user);

        $manager->flush();
    }
}