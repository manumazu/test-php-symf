<?php


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WebTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertResponseStatusCodeSame(404);
        $this->assertSelectorTextContains('h1', 'Welcome');
    }
}