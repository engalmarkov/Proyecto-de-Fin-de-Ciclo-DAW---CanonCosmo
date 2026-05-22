<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class DebugExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        // El 9999 es para ser los PRIMEROS en enterarnos del error,
        // antes de que Symfony intente usar su "HtmlErrorRenderer" roto.
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 9999],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        // Cazamos el error original
        $error = $event->getThrowable();
        
        // dd() significa "Imprime esto en crudo y MATA la ejecución".
        // Así evitamos que Symfony haga nada más.
        dd([
            'MENSAJE_REAL' => $error->getMessage(),
            'ARCHIVO_DONDE_FALLA' => $error->getFile(),
            'LINEA' => $error->getLine()
        ]);
    }
}