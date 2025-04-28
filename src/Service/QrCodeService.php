<?php

namespace App\Service;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class QrCodeService
{
    private string $publicPath;

    public function __construct(ParameterBagInterface $params)
    {
        $this->publicPath = $params->get('kernel.project_dir') . '/public';
    }

    public function generateQrCode(string $data): string
    {
        $qrCode = new QrCode($data);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        $fileName = 'uploads/qrcodes/qr_' . uniqid() . '.png';
        $fullPath = $this->publicPath . '/' . $fileName;

        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0777, true);
        }

        file_put_contents($fullPath, $result->getString());

        return '/' . $fileName;
    }
}