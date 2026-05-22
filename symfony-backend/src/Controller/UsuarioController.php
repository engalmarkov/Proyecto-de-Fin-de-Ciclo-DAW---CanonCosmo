<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
class UsuarioController extends AbstractController
{
    // Ver perfil propio
    #[Route('/perfil', name: 'api_perfil_ver', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function verPerfil(): JsonResponse
    {
        /** @var Usuario $usuario */
        $usuario = $this->getUser();

        return $this->json($usuario, JsonResponse::HTTP_OK, [], [
            'groups' => ['usuario:read']
        ]);
    }

    // Actualizar perfil propio
    #[Route('/perfil', name: 'api_perfil_update', methods: ['PUT'])] // Corregida la ruta
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function update(Request $request, EntityManagerInterface $em): JsonResponse
    {
        /** @var Usuario $usuario */
        $usuario = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (isset($data['nombre'])) $usuario->setNombre($data['nombre']);
        if (isset($data['apellidos'])) $usuario->setApellidos($data['apellidos']);
        
        $em->flush();

        return $this->json(['message' => 'Perfil actualizado correctamente']);
    }

    // ADMIN: Listar todos los usuarios
    #[Route('/admin/usuarios', name: 'api_admin_usuarios_list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function listarUsuarios(UsuarioRepository $usuarioRepository): JsonResponse
    {
        $usuarios = $usuarioRepository->findAll();

        return $this->json($usuarios, JsonResponse::HTTP_OK, [], [
            'groups' => ['usuario:read']
        ]);
    }

    // ADMIN: Activar/Desactivar usuario
    #[Route('/admin/usuarios/{id}/estado', name: 'api_admin_usuario_toggle', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function toggleEstado(Usuario $usuario, EntityManagerInterface $em): JsonResponse
    {
        $usuario->setActivo(!$usuario->isActivo());
        $em->flush();

        return $this->json([
            'message' => 'Estado del usuario actualizado correctamente.',
            'nuevo_estado' => $usuario->isActivo()
        ]);
    }
}