<?php

namespace App\Http\Controllers;

abstract class Controller
{
    function uploadImage($image, $imageName)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'https://api.imgbb.com/1/upload', [
            'form_params' => [
                'image' => base64_encode(file_get_contents($image->getRealPath())),
                'name' => $imageName,
                'key' => env('IMAGEBB_API_KEY'),
            ],
        ]);
        $json_decode = json_decode($response->getBody(), true);
        return $json_decode['data']['url'];
    }
}
