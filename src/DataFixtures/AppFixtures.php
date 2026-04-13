<?php

namespace App\DataFixtures;

use App\Entity\Producto;
use App\Entity\Usuario;
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
        // 1. CREAR USUARIOS (Admin y Cliente)
        $admin = new Usuario();
        $admin->setEmail('admin@frikistore.com');
        $admin->setNombre('Ángel');
        $admin->setApellidos('Admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        $cliente = new Usuario();
        $cliente->setEmail('cliente@gmail.com');
        $cliente->setNombre('Juan');
        $cliente->setApellidos('Pruebas');
        $cliente->setRoles(['ROLE_USER']);
        $cliente->setPassword($this->hasher->hashPassword($cliente, 'cliente123'));
        $manager->persist($cliente);

        // 2. LISTA DE CATEGORÍAS Y PRODUCTOS "FRIKIS"
        $categorias = ['Figuras', 'Cartas TCG', 'Cómics', 'Merchandising'];
        
        $productosData = [
            ['nombre' => 'Figura Luffy Gear 5', 'precio' => '185.00', 'cat' => 'Figuras', 'stock' => 5],
            ['nombre' => 'Figura Goku Ultra Instinto', 'precio' => '45.99', 'cat' => 'Figuras', 'stock' => 12],
            ['nombre' => 'Caja Pokémon Escarlata y Púrpura', 'precio' => '120.00', 'cat' => 'Cartas TCG', 'stock' => 20],
            ['nombre' => 'Manga One Piece Vol. 100', 'precio' => '7.50', 'cat' => 'Cómics', 'stock' => 50],
            ['nombre' => 'Figura Naruto Shippuden Kizuna', 'precio' => '65.00', 'cat' => 'Figuras', 'stock' => 0], // Sin stock para probar avisos
            ['nombre' => 'Llavero Dragon Ball Z', 'precio' => '4.95', 'cat' => 'Merchandising', 'stock' => 100],
            ['nombre' => 'Sudadera Akatsuki', 'precio' => '35.00', 'cat' => 'Merchandising', 'stock' => 15],
            ['nombre' => 'Figura Iron Man Mark 85', 'precio' => '95.00', 'cat' => 'Figuras', 'stock' => 3],
        ];

        foreach ($productosData as $data) {
            $producto = new Producto();
            $producto->setNombre($data['nombre']);
            $producto->setPrecio($data['precio']);
            $producto->setCategoria($data['cat']);
            $producto->setStock($data['stock']);
            $producto->setDescripcion('Descripción profesional de ' . $data['nombre'] . ' ideal para coleccionistas.');
            $producto->setImagenUrl('https://picsum.photos/400/400?random=' . rand(1, 1000));
            $producto->setActivo(true);
            
            $manager->persist($producto);
        }

        $manager->flush();
    }
}