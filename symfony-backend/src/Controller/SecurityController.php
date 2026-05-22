<?php

namespace App\Controller;

use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/api/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): JsonResponse
    {
        /** @var Usuario|null $user */
        $user = $this->getUser();

        if ($user) {
            return $this->json([
                'message' => 'Ya estás autenticado',
                'user' => [
                    'id'        => $user->getId(),
                    'email'     => $user->getEmail(),
                    'nombre'    => $user->getNombre(),
                    'apellidos' => $user->getApellidos(),
                ]
            ]);
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->json([
            'last_username' => $lastUsername,
            'error' => $error ? $error->getMessageKey() : null,
        ]);
    }

    #[Route('/api/registro', name: 'app_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        
        if ($this->getUser()) {
            return $this->json(['message' => 'Ya tienes una sesión activa.'], JsonResponse::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['password'], $data['nombre'], $data['apellidos'])) {
            return $this->json([
                'error' => 'Todos los campos son obligatorios (email, password, nombre, apellidos).'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $existingUser = $entityManager->getRepository(Usuario::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return $this->json(['error' => 'El email ya está registrado.'], JsonResponse::HTTP_CONFLICT);
        }

        $user = new Usuario();
        $user->setEmail($data['email']);
        $user->setNombre($data['nombre']);
        $user->setApellidos($data['apellidos']);
        $user->setPassword($userPasswordHasher->hashPassword($user, $data['password']));

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'message' => '¡Cuenta creada con éxito!',
            'user' => [
                'email'  => $user->getEmail(),
                'nombre' => $user->getNombre(),
            ]
        ], JsonResponse::HTTP_CREATED);
    }
}