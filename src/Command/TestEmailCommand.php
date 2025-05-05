<?php
// src/Command/TestEmailCommand.php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class TestEmailCommand extends Command
{
    protected static $defaultName = 'app:test-email';
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        parent::__construct();
        $this->mailer = $mailer;
    }

    protected function configure()
    {
        $this->setDescription('Test email sending functionality');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (new Email())
            ->from('ghofranebenhassen64@gmail.com')
            ->to('benhassenghofrane14@gmail.com') // Change to your test email
            ->subject('Email Test from Symfony')
            ->text('This is a test email from Symfony Mailer');

        try {
            $this->mailer->send($email);
            $output->writeln('<info>Email sent successfully!</info>');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>Error sending email: '.$e->getMessage().'</error>');
            return Command::FAILURE;
        }
    }
}