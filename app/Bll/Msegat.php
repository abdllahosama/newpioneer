<?php

namespace App\Bll;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;

class Msegat
{
    protected $apiKey;
    protected $userName;
    protected $numbers;
    protected $userSender;
    protected $msg;

    public function __construct($numbers, $msg)
    {
        $this->userName = 'CFund';
        $this->apiKey = '9976bbd14e9b262570ff5c663a232478';
        $this->numbers = $numbers;
        $this->userSender = 'ALJISER';
        $this->msg = $msg;
    }

    // check Balance

    private function checkBalance()
    {
        $client = new Client();

        $url = 'https://www.msegat.com/gw/Credits.php';
        $headers = [
            'Content-Type' => 'multipart/form-data; boundary=BOUNDARY'
        ];
        $boundary = 'BOUNDARY';
        $body = new MultipartStream([
            [
                'name' => 'userName',
                'contents' => $this->userName
            ],
            [
                'name' => 'apiKey',
                'contents' => $this->apiKey
            ]
        ], $boundary);

        $request = new Request('POST', $url, $headers, $body);
        $response = $client->send($request);

        $res = $response->getBody()->getContents();
        if ($res && intval($res)) {
            return $res;
        }
        return false;
    }

    // send SMS
    public function sendSMS()
    {

        // checkBalance
        $checkBalance = $this->checkBalance();
        if (!$checkBalance && $checkBalance <= 1) {
            return 'No Balance';
        }
        $client = new Client();

        $url = 'https://www.msegat.com/gw/sendsms.php';
        $headers = [
            'Content-Type' => 'application/json',
        ];
        $body = json_encode([
            'userName' => $this->userName,
            'numbers' => $this->numbers,
            'userSender' => $this->userSender,
            'apiKey' => $this->apiKey,
            'msg' => $this->msg,
        ]);

        $response = $client->post($url, [
            'headers' => $headers,
            'body' => $body,
        ]);

        $res = $response->getBody()->getContents();
        $data = json_decode($res, true);

        if (isset($data['code']) && $data['code'] === '1') {
            return $data['message']; // Return the success message
        } else {
            return false; // Return 'No Balance'
        }

    }

}
