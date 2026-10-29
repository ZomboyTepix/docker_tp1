<?php

use Symfony\Component\Dotenv\Dotenv;

// On charge les classes du projet, puis les valeurs d'environnement par défaut.
require dirname(__DIR__).'/vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
