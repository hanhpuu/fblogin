<?php
/**
 * Created by PhpStorm.
 * User: dungdc40
 * Date: 10/5/2019
 * Time: 5:15 PM
 */

namespace App\Services;


class Bitly
{
    public static $MAX_NUM_TRY = 2;


    public static function extractTokenFromOauthResponse($body)
    {
        $params = explode('&', $body);

        foreach ($params as $param) {
            $paramPart = explode('=', $param);
            if ($paramPart[0] === 'access_token') {
                return $paramPart[1];
            }
        }
        return null;
    }

    public static function getShortenLinkByFbIds($rows, $token)
    {
        $client = new \GuzzleHttp\Client();
        $result = [];

        foreach ($rows as $i => $row) {
            if ($i === 0) {
                continue;
            }
            $url = $row[0];
            try {
                $result[] = ['link' => self::tryShortenLink($client, $url, $token)];
            } catch (\Exception $e) {
                $result[] = ['link' => null, 'error' => $e->getMessage()];
            }
        }
        return $result;
    }

    public static function tryShortenLink($client, $url, $token)
    {

        for ($i = 0; $i < self::$MAX_NUM_TRY; $i++) {
            try {
                $res = $client->request('POST', env('BITLY_API_URL') . 'v4/shorten', ['json' => [
                    'long_url' => $url
                ], 'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ]]);
                $body = json_decode($res->getBody());
                return $body->link;

            } catch (\Exception $e) {
                // reach max try
                if ($i == self::$MAX_NUM_TRY - 1) {
                    throw new \Exception($e->getMessage());
                }
            }

        }
    }
}
