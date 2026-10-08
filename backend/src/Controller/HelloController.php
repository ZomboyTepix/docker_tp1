<?php

// src/Controller/HelloController.php
// Contrôleur Symfony minimal pour retourner un "Hello World" en JSON

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HelloController
{
    /**
     * Route GET /api/hello
     * Retourne un message JSON de bienvenue
     */
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

    /**
     * Route GET / – health check basique
     */
    #[Route('/', name: 'app_root', methods: ['GET'])]
    public function root(): JsonResponse
    {
        return new JsonResponse([
            'status' => 'OK',
            'service' => 'tp-docker-backend',
        ]);
    }
}
