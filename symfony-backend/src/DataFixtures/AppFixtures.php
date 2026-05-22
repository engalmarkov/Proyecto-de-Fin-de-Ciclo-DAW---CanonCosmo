<?php

namespace App\DataFixtures;

use App\Entity\Categoria;
use App\Entity\Producto;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 1. CREAMOS LAS CATEGORÍAS
        $categoriasData = ['Videojuegos', 'Anime', 'Cómics', 'Figuras'];
        $categoriasObjetos = [];

        foreach ($categoriasData as $nombreCat) {
            $categoria = new Categoria();
            $categoria->setNombre($nombreCat);
            $manager->persist($categoria);
            
            // Guardamos la referencia para usarla luego en los productos
            $categoriasObjetos[$nombreCat] = $categoria;
        }

        // 2. LISTA DE PRODUCTOS DE PRUEBA
        $productosData = [
            [
                'nombre' => 'Figura Iron Man Mark 85',
                'precio' => '95.00',
                'oferta' => null, 
                'stock' => 12,
                'descripcion' => 'Réplica detallada a escala 1/10 de la armadura Mark 85 de Avengers: Endgame. Incluye luces LED y manos intercambiables.',
                'imagen' => 'https://images.unsplash.com/photo-1608889175123-8ec330b86f84?w=500&auto=format&fit=crop&q=60',
                'categoria' => 'Figuras'
            ],
            [
                'nombre' => 'Espada Maestra - Zelda TotK',
                'precio' => '149.99',
                'oferta' => null, // <-- Producto SIN oferta
                'stock' => 5,
                'descripcion' => 'Réplica de metal de tamaño real de la Espada Maestra destructora del mal, tal y como aparece en Tears of the Kingdom.',
                'imagen' => 'https://images.unsplash.com/photo-1593305841991-05c297ba4575?w=500&auto=format&fit=crop&q=60',
                'categoria' => 'Videojuegos'
            ],
            [
                'nombre' => 'Funko Pop! Naruto Modo Sabio',
                'precio' => '15.95',
                'oferta' => '12.50',
                'stock' => 25,
                'descripcion' => 'Figura de vinilo coleccionable de Naruto Uzumaki en su icónico modo sabio de los seis caminos. Edición especial.',
                'imagen' => 'https://images.unsplash.com/photo-1578632767115-351597cf2477?w=500&auto=format&fit=crop&q=60',
                'categoria' => 'Anime'
            ],
            [
                'nombre' => 'Cómic Batman: Año Uno',
                'precio' => '22.00',
                'oferta' => null,
                'stock' => 8,
                'descripcion' => 'Edición deluxe en tapa dura de la legendaria obra de Frank Miller y David Mazzucchelli. El origen definitivo del caballero oscuro.',
                'imagen' => 'https://images.unsplash.com/photo-1601647998801-7ec8223692d9?w=500&auto=format&fit=crop&q=60',
                'categoria' => 'Cómics'
            ],
            [
                'nombre' => 'Katana de Demon Slayer (Tanjiro)',
                'precio' => '85.00',
                'oferta' => '69.90',
                'stock' => 10,
                'descripcion' => 'Réplica decorativa de la espada Nichirin negra de Tanjiro Kamado. Hoja de acero al carbono no afilada.',
                'imagen' => 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?w=500&auto=format&fit=crop&q=60',
                'categoria' => 'Anime'
            ]
        ];

        // 3. REGISTRAMOS LOS PRODUCTOS ASOCIÁNDOLOS A SUS CATEGORÍAS
        foreach ($productosData as $pData) {
            $producto = new Producto();
            $producto->setNombre($pData['nombre']);
            $producto->setPrecio($pData['precio']);
            
            // Añadimos la lógica de la oferta
            if ($pData['oferta'] !== null) {
                $producto->setPrecioOferta($pData['oferta']);
            }

            $producto->setStock($pData['stock']);
            $producto->setDescripcion($pData['descripcion']);
            $producto->setImagenUrl($pData['imagen']);
            $producto->setActivo(true);
            
            $categoriaAsociada = $categoriasObjetos[$pData['categoria']];
            $producto->setCategoria($categoriaAsociada);

            $manager->persist($producto);
        }

        $manager->flush();
    }
}