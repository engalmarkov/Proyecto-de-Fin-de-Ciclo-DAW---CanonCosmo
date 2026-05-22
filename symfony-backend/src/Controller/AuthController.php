<?php

namespace App\Controller;

use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
class AuthController extends AbstractController
{
    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): JsonResponse 
    {
        $data = json_decode($request->getContent(), true);

        // 1. Validación de campos obligatorios
        if (!isset($data['email'], $data['password'], $data['nombre'], $data['apellidos'])) {
            return $this->json([
                'error' => 'Faltan campos: email, password, nombre, apellidos son obligatorios.'
            ], JsonResponse::HTTP_BAD_REQUEST); // Usamos JsonResponse para el código 400
        }

        // 2. Comprobar email duplicado
        $existente = $em->getRepository(Usuario::class)->findOneBy(['email' => $data['email']]);
        if ($existente) {
            return $this->json([
                'error' => 'Este email ya está registrado.'
            ], JsonResponse::HTTP_CONFLICT); // Código 409
        }

        // 3. Crear el usuario
        $usuario = new Usuario();
        $usuario->setEmail($data['email']);
        $usuario->setNombre($data['nombre']);
        $usuario->setApellidos($data['apellidos']);

        // Hashear contraseña
        $hashedPassword = $passwordHasher->hashPassword($usuario, $data['password']);
        $usuario->setPassword($hashedPassword);

        // 4. Validar restricciones de la Entidad (@Assert)
        $errors = $validator->validate($usuario);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json([
                'errors' => $errorMessages
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY); // Código 422
        }

        // 5. Guardar
        $em->persist($usuario);
        $em->flush();

        return $this->json([
            'message' => 'Usuario registrado correctamente.',
            'usuario' => [
                'id'        => $usuario->getId(),
                'email'     => $usuario->getEmail(),
                'nombre'    => $usuario->getNombre(),
                'apellidos' => $usuario->getApellidos()
            ]
        ], JsonResponse::HTTP_CREATED); // Código 201
    }
}