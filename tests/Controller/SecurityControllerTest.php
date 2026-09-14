<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testAnonymousAccessToProtectedRouteRedirectsToLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/livros/');

        $this->assertResponseRedirects('/login');
    }

    public function testLoginWithValidCredentialsGrantsAccess(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $client->submit($crawler->selectButton('Entrar')->form([
            '_username' => 'admin@teste-spassu.local',
            '_password' => 'Livraria@2026',
        ]));

        $this->assertResponseRedirects('/');
        $client->followRedirect();

        $client->request('GET', '/livros/');
        $this->assertResponseIsSuccessful();
    }

    public function testLoginWithInvalidCredentialsShowsError(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $client->submit($crawler->selectButton('Entrar')->form([
            '_username' => 'admin@teste-spassu.local',
            '_password' => 'senha-errada',
        ]));

        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorTextContains('[data-flash-alert-type-value="error"]', 'Credenciais inválidas');
    }
}
