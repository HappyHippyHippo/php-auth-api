<?php

namespace App\Tests;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class EndpointTester extends WebTestCase
{
    /** @var KernelBrowser */
    protected KernelBrowser $client;

    /** @var EntityManagerInterface */
    protected EntityManagerInterface $localManager;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();

        $em = static::getContainer()->get('doctrine.orm.local_entity_manager');
        if (!($em instanceof EntityManagerInterface)) {
            $this->fail('unable to retrieve the local entity manager');
        }
        $this->localManager = $em;

        $schemaTool = new SchemaTool($this->localManager);
        $schemaTool->updateSchema($this->localManager->getMetadataFactory()->getAllMetadata());
    }

    /**
     * @param Fixture $fixture
     * @param bool $purge
     * @return void
     */
    protected function loadFixtures(Fixture $fixture, bool $purge = true): void
    {
        $loader = new Loader();
        $loader->addFixture($fixture);

        $executor = new ORMExecutor($this->localManager, $purge ? new ORMPurger() : null);
        $executor->execute($loader->getFixtures());
    }
}
