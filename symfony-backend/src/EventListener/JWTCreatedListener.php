<?php

namespace App\EventListener;

use App\Entity\Usuario;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();

        // Si no es nuestro usuario, no hacemos nada
        if (!$user instanceof Usuario) {
            return;
        }

        // Sacamos los datos que ya van a ir en el JSON
        $payload = $event->getData();

        // Metemos la información que Angular está deseando ver
        $payload['user'] = [
            'id' => $user->getId(),
            'nombre' => $user->getNombre(),
            'apellidos' => $user->getApellidos(),
            'email' => $user->getEmail(),
        ];

        // Guardamos los datos actualizados
        $event->setData($payload);
    }
}