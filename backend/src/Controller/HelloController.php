<?php

// Contrôleur minimal utilisé pour vérifier le fonctionnement de la stack Docker.

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class HelloController extends AbstractController
{
    #[Route('/api/hello', name: 'app_hello', methods: ['GET'])]
    public function hello(): JsonResponse
    {
        return new JsonResponse([
            'message'     => 'Hello World 🐳',
            'service'     => 'Backend Symfony',
            'description' => 'TP Docker – Image backend personnalisée',
            'php_version' => PHP_VERSION,
            'framework'   => 'Symfony 7',
        ]);
    }

    // Cette route concerne Symfony ; Nginx envoie la racine publique à React.
    #[Route('/', name: 'app_root', methods: ['GET'])]
    public function root(): JsonResponse
    {
        return new JsonResponse([
            'status' => 'OK',
            'service' => 'tp-docker-backend',
        ]);
    }
}
