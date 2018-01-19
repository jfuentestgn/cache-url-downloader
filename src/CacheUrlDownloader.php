<?php
/**
 * Created by PhpStorm.
 * User: jfuentes
 * Date: 08/01/2018
 * Time: 20:03
 */

namespace JFuentesTgn\CacheUrlDownloader;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Facades\Hash;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7;

class CacheUrlDownloader implements UrlDownloader
{
    protected $delegate;
    protected $cache;

    public function __construct(UrlDownloader $delegate, Cache $cache, array $config = [])
    {
        $this->delegate = $delegate;
        $this->cache = $cache;
    }


    public function downloadUrl(string $url, $options = []) : ResponseInterface
    {
        $hash = self::getUrlHash($url);
        if ($this->cache->has($hash)) {
            return $this->cacheGet($hash);
        }

        $response = $this->delegate->downloadUrl($url);
        $this->cachePut($hash, $response);

        return $response;
    }

    public static function getUrlHash($url)
    {
        //return Hash::make($url);
        return md5($url);
    }


    private function cachePut($hash, $resp)
    {
        $data = Psr7\str($resp);
        $this->cache->put($hash, $data, 1440);
    }

    private function cacheGet($hash)
    {
        $data = $this->cache->get($hash);
        return Psr7\parse_response($data);
    }
}