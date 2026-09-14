<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Base para os testes funcionais de CRUD: limpa as tabelas de domínio e
 * autentica o usuário de demonstração semeado pela migration, já que todas
 * as rotas exigem ROLE_USER.
 */
abstract class AuthenticatedWebTestCase extends WebTestCase
{
    protected EntityManagerInterface $em;
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = self::getContainer()->get(EntityManagerInterface::class);

        $connection = $this->em->getConnection();
        $connection->executeStatement('DELETE FROM livro_autor');
        $connection->executeStatement('DELETE FROM livro_assunto');
        $connection->executeStatement('DELETE FROM livro');
        $connection->executeStatement('DELETE FROM autor');
        $connection->executeStatement('DELETE FROM assunto');

        $admin = $this->em->getRepository(User::class)->findOneBy(['email' => 'admin@teste-spassu.local']);
        $this->client->loginUser($admin);
    }
}
