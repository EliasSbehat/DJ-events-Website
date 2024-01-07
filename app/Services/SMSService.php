<?php
namespace App\Services;

use Twilio\Rest\Client;

function formatUKMobileNumber($number) {
    // Remove spaces from the number
    $number = str_replace(' ', '', $number);

    // Check if the number matches the UK mobile phone number pattern
    if (preg_match('/^((\+44)?0?7\d{9})$/', $number, $matches)) {
        // If it doesn't have the +44 prefix
        if (strpos($number, '+44') === false) {
            // Add the +44 prefix and remove the leading 0
            return '+44' . substr($matches[1], 1);
        }
        return $matches[1];
    }
    // If it's not a UK mobile number, return it as it is
    return $number;
}

class SMSService
{
    protected $client;

    public function __construct()
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');

        $this->client = new Client($sid, $token);
    }

    public function send($to, $message)
    {
        $to = formatUKMobileNumber($to);
        $from = config('services.twilio.from_number');
        return $this->client->messages->create($to, [
            'from' => $from,
            'body' => $message,
        ]);
    }
}
