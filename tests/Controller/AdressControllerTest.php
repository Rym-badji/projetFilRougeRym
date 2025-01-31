<?php

namespace App\Tests\Controller;

use App\Entity\Adress;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AdressControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $adressRepository;
    private string $path = '/adress/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->adressRepository = $this->manager->getRepository(Adress::class);

        foreach ($this->adressRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Adress index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'adress[title]' => 'Testing',
            'adress[fname]' => 'Testing',
            'adress[lname]' => 'Testing',
            'adress[address]' => 'Testing',
            'adress[city]' => 'Testing',
            'adress[cp]' => 'Testing',
            'adress[country]' => 'Testing',
            'adress[user]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->adressRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Adress();
        $fixture->setTitle('My Title');
        $fixture->setFname('My Title');
        $fixture->setLname('My Title');
        $fixture->setAddress('My Title');
        $fixture->setCity('My Title');
        $fixture->setCp('My Title');
        $fixture->setCountry('My Title');
        $fixture->setUser('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Adress');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Adress();
        $fixture->setTitle('Value');
        $fixture->setFname('Value');
        $fixture->setLname('Value');
        $fixture->setAddress('Value');
        $fixture->setCity('Value');
        $fixture->setCp('Value');
        $fixture->setCountry('Value');
        $fixture->setUser('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'adress[title]' => 'Something New',
            'adress[fname]' => 'Something New',
            'adress[lname]' => 'Something New',
            'adress[address]' => 'Something New',
            'adress[city]' => 'Something New',
            'adress[cp]' => 'Something New',
            'adress[country]' => 'Something New',
            'adress[user]' => 'Something New',
        ]);

        self::assertResponseRedirects('/adress/');

        $fixture = $this->adressRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getFname());
        self::assertSame('Something New', $fixture[0]->getLname());
        self::assertSame('Something New', $fixture[0]->getAddress());
        self::assertSame('Something New', $fixture[0]->getCity());
        self::assertSame('Something New', $fixture[0]->getCp());
        self::assertSame('Something New', $fixture[0]->getCountry());
        self::assertSame('Something New', $fixture[0]->getUser());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Adress();
        $fixture->setTitle('Value');
        $fixture->setFname('Value');
        $fixture->setLname('Value');
        $fixture->setAddress('Value');
        $fixture->setCity('Value');
        $fixture->setCp('Value');
        $fixture->setCountry('Value');
        $fixture->setUser('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/adress/');
        self::assertSame(0, $this->adressRepository->count([]));
    }
}
