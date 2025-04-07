<?php

namespace App\command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailTestCommand extends Command
{
    protected static $defaultName = 'app:test-email';

    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Test Email Command');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = (new Email())
            ->from('mejrieya384@gmail.com')
            ->to('destinataire@example.com')
            ->subject('Test Email')
            ->text('Ceci est un test d\'email.');

        try {
            $this->mailer->send($email);
            $output->writeln('Email envoyé avec succès!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('Erreur lors de l\'envoi de l\'email: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
