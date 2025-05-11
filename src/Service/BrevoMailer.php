<?php 
namespace App\Service;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class BrevoMailer
{
    private $apiInstance;

    public function __construct(ParameterBagInterface $params)
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $params->get('brevo_api_key'));
        $this->apiInstance = new TransactionalEmailsApi(new Client(), $config);
    }

    public function sendEmail(string $to, string $subject, string $htmlContent): void
    {
        $email = new SendSmtpEmail([
            'subject' => $subject,
            'htmlContent' => $htmlContent,
            'sender' => ['name' => 'EasyWay', 'email' => 'tayssirbennejma@gmail.com'],
            'to' => [['email' => $to]]
        ]);

        $this->apiInstance->sendTransacEmail($email);
    }
}
