<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TestControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/test');

        self::assertResponseIsSuccessful();
    }

    #[Route('/test-email', name: 'test_email')]
    public function testEmail(MailerService $mailerService): Response
    {
        $mailerService->sendTestEmail('client@example.com');

        return new Response('Test email sent successfully!');
    }
}
