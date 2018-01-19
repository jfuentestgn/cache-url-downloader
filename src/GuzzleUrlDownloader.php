<?php
/**
 * Created by PhpStorm.
 * User: jfuentes
 * Date: 08/01/2018
 * Time: 20:03
 */

namespace JFuentesTgn\CacheUrlDownloader;


use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Client as GuzzleClient;


class GuzzleUrlDownloader implements UrlDownloader
{

    public function __construct(array $config = [])
    {

    }


    public function downloadUrl(string $url, $options = []) : ResponseInterface
    {
        $client = new GuzzleClient();
        try {
            $response = $client->request('GET', $url, ['http_errors' => false, 'verify' => false]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $response;
    }
}