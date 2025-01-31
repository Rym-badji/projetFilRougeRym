<?php

namespace App\Tests\Controller;

use App\Entity\PrivacyPolicy;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PrivacyPolicyControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $privacyPolicyRepository;
    private string $path = '/privacy/policy/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->privacyPolicyRepository = $this->manager->getRepository(PrivacyPolicy::class);

        foreach ($this->privacyPolicyRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('PrivacyPolicy index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'privacy_policy[title]' => 'Testing',
            'privacy_policy[content]' => 'Testing',
            'privacy_policy[updatedAt]' => 'Testing',
            'privacy_policy[createdAt]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->privacyPolicyRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new PrivacyPolicy();
        $fixture->setTitle('My Title');
        $fixture->setContent('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setCreatedAt('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('PrivacyPolicy');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new PrivacyPolicy();
        $fixture->setTitle('Value');
        $fixture->setContent('Value');
        $fixture->setUpdatedAt('Value');
        $fixture->setCreatedAt('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'privacy_policy[title]' => 'Something New',
            'privacy_policy[content]' => 'Something New',
            'privacy_policy[updatedAt]' => 'Something New',
            'privacy_policy[createdAt]' => 'Something New',
        ]);

        self::assertResponseRedirects('/privacy/policy/');

        $fixture = $this->privacyPolicyRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getContent());
        self::assertSame('Something New', $fixture[0]->getUpdatedAt());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new PrivacyPolicy();
        $fixture->setTitle('Value');
        $fixture->setContent('Value');
        $fixture->setUpdatedAt('Value');
        $fixture->setCreatedAt('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/privacy/policy/');
        self::assertSame(0, $this->privacyPolicyRepository->count([]));
    }
}
