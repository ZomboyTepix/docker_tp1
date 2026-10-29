<?php

namespace App\Tests\Controller;

use App\Kernel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class HelloControllerTest extends TestCase
{
    public function testLaRouteHelloRetourneDuJson(): void
    {
        $reponse = $this->envoyerRequete('GET', '/api/hello');

        self::assertSame(200, $reponse->getStatusCode());
        self::assertStringStartsWith('application/json', (string) $reponse->headers->get('Content-Type'));

        $donnees = json_decode($reponse->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('Hello World', $donnees['message'] ?? null);
        self::assertSame('Backend Symfony', $donnees['service'] ?? null);
    }

    public function testLaRacineSymfonyRepond(): void
    {
        // Ici on teste Symfony directement, pas la page React servie par Nginx.
        $reponse = $this->envoyerRequete('GET', '/');

        self::assertSame(200, $reponse->getStatusCode());
        $donnees = json_decode($reponse->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('OK', $donnees['status'] ?? null);
    }

    public function testLaRouteHelloRefusePost(): void
    {
        $reponse = $this->envoyerRequete('POST', '/api/hello');

        self::assertSame(405, $reponse->getStatusCode());
        self::assertStringContainsString('GET', (string) $reponse->headers->get('Allow'));
    }

    public function testUneRouteInconnueRetourne404(): void
    {
        $reponse = $this->envoyerRequete('GET', '/api/inconnue');

        self::assertSame(404, $reponse->getStatusCode());
    }

    private function envoyerRequete(string $methode, string $chemin): Response
    {
        // Chaque requête utilise un noyau Symfony en environnement de test.
        $noyau = new Kernel('test', false);

        try {
            // La requête est traitée en mémoire : aucun serveur HTTP n'est nécessaire.
            $requete = Request::create('http://127.0.0.1'.$chemin, $methode);
            $reponse = $noyau->handle($requete);
            $noyau->terminate($requete, $reponse);

            return $reponse;
        } finally {
            $noyau->shutdown();
        }
    }
}
