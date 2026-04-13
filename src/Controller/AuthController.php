<?php

namespace App\Controller;

use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Validar que vengan los campos necesarios
        if (!isset($data['email'], $data['password'], $data['nombre'], $data['apellidos'])) {
            return $this->json([
                'error' => 'Faltan campos: email, password, nombre, apellidos son obligatorios.'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Comprobar que el email no esté ya registrado
        $existente = $em->getRepository(Usuario::class)->findOneBy(['email' => $data['email']]);
        if ($existente) {
            return $this->json(['error' => 'Este email ya está registrado.'], Response::HTTP_CONFLICT);
        }

        // Crear el usuario
        $usuario = new Usuario();
        $usuario->setEmail($data['email']);
        $usuario->setNombre($data['nombre']);
        $usuario->setApellidos($data['apellidos']);

        // Hashear la contraseña con bcrypt
        $hashedPassword = $passwordHasher->hashPassword($usuario, $data['password']);
        $usuario->setPassword($hashedPassword);

        // Validar con las restricciones de la entidad (@Assert)
        $errors = $validator->validate($usuario);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Persistir en BBDD (evidencia de guardado)
        $em->persist($usuario);
        $em->flush();

        return $this->json([
            'message' => 'Usuario registrado correctamente.',
            'usuario' => [
                'id'        => $usuario->getId(),
                'email'     => $usuario->getEmail(),
                'nombre'    => $usuario->getNombre(),
                'apellidos' => $usuario->getApellidos(),
                'createdAt' => $usuario->getCreatedAt()->format('Y-m-d H:i:s'),
            ]
        ], Response::HTTP_CREATED);
    }
}