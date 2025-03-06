<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendTestEmail(string $to): void
    {
        $email = (new Email())
            ->from('your_email@gmail.com')
            ->to($to)
            ->subject('Test Email from Symfony')
            ->text('This is a test email to check if the mailing service works.')
            ->html('<p>This is a <strong>test email</strong> sent from Symfony.</p>');

        $this->mailer->send($email);
    }
}
