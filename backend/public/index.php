<?php

// public/index.php – Point d'entrée de l'application Symfony
// Ce fichier est le seul exposé au serveur web (document root = public/)

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
