<?php
namespace App\Command;

use App\Repository\PostsRepository;
use App\Service\SmsService; // Correct the service import
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendPostReminderCommand extends Command
{
    protected static $defaultName = 'app:send-post-reminders';
    
    private $postsRepository;
    private $smsService; // Correct this to SmsService

    public function __construct(PostsRepository $postsRepository, SmsService $smsService) // Inject SmsService here
    {
        parent::__construct();
        $this->postsRepository = $postsRepository;
        $this->smsService = $smsService; // Assign SmsService to the variable
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $today = new \DateTime();
        $posts = $this->postsRepository->findBy(['date' => $today]);
    
        foreach ($posts as $post) {
            $user = $post->getUser();
            if ($user && $user->getTelephonne()) {
                $phoneNumber = '+216' . $user->getTelephonne(); // adjust depending on your number storage
                $message = sprintf(
                    "Bonjour %s, votre covoiturage de %s à %s est prévu aujourd'hui.",
                    $user->getNom(),
                    $post->getVilleDepart(),
                    $post->getVilleArrivee()
                );
    
                $this->smsService->sendSms($phoneNumber, $message); // Use the correct SmsService
            }
        }
    
        $output->writeln('SMS notifications sent.');
    
        return Command::SUCCESS;
    }
}
