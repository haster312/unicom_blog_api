<?php


namespace App\Helpers;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class Firebase
{
    public $firebase;
    public $messaging;

    public function __construct()
    {
        $this->firebase = (new Factory)->withServiceAccount(config_path('firebase_credentials.json'));
        $this->messaging = $this->firebase->createMessaging();
    }

    public function sendMessage($token, $data)
    {
        $title = $data['title'];
        $body = $data['body'];
        $data = $data['meta'];

        $message = CloudMessage::fromArray([
            'token' => $token,
            'notification' => [ 'title' => $title, 'body' => $body],
            'data' => $data
        ]);

        Log::error('Push notification for: ' . $token);
        $this->messaging->send($message);
    }
}
