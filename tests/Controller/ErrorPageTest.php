<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ErrorPageTest extends WebTestCase
{
    public function testNotFoundPageUsesAppTheme(): void
    {
        // debug:false é obrigatório aqui: com debug ligado o Symfony sempre
        // mostra a página de exceção crua, ignorando o template customizado.
        $client = static::createClient(['debug' => false]);

        $em = self::getContainer()->get(EntityManagerInterface::class);
        $admin = $em->getRepository(User::class)->findOneBy(['email' => 'admin@teste-spassu.local']);
        $client->loginUser($admin);

        $client->request('GET', '/livros/999999/editar');

        $this->assertResponseStatusCodeSame(404);
        $this->assertSelectorExists('nav.navbar');
        $this->assertSelectorTextContains('body', 'Página não encontrada');
    }
}
