<?php

namespace App\Mail;

use Illuminate\Mail\Transport\Transport;
use Illuminate\Support\Facades\Http;
use Swift_Mime_SimpleMessage;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;
use Symfony\Component\Mime\Email;

class ResendTransport extends AbstractTransport
{
    protected $apiKey;

    public function __construct($apiKey)
    {
        parent::__construct();
        $this->apiKey = $apiKey;
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        
        $payload = [
            'from' => $this->getFromAddress($email),
            'to' => $this->getToAddresses($email),
            'subject' => $email->getSubject(),
        ];

        // Add HTML body if present
        if ($email->getHtmlBody()) {
            $payload['html'] = $email->getHtmlBody();
        }

        // Add text body if present
        if ($email->getTextBody()) {
            $payload['text'] = $email->getTextBody();
        }

        // Send via Resend API
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.resend.com/emails', $payload);

        if (!$response->successful()) {
            throw new \Exception('Resend API Error: ' . $response->body());
        }
    }

    protected function getFromAddress(Email $email): string
    {
        $from = $email->getFrom();
        if (empty($from)) {
            return config('mail.from.address');
        }
        
        $address = array_key_first($from);
        $name = $from[$address];
        
        return $name ? "{$name} <{$address}>" : $address;
    }

    protected function getToAddresses(Email $email): array
    {
        $to = $email->getTo();
        $addresses = [];
        
        foreach ($to as $address => $name) {
            $addresses[] = $name ? "{$name} <{$address}>" : $address;
        }
        
        return $addresses;
    }

    public function __toString(): string
    {
        return 'resend';
    }
}
