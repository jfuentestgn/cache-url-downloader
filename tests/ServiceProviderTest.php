<?php
/**
 * Created by PhpStorm.
 * User: jfuentes
 * Date: 10/01/2018
 * Time: 22:17
 */

use Illuminate\Support\Facades\Cache;
use JFuentesTgn\CacheUrlDownloader\CacheUrlDownloader;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7;

class ServiceProviderTest extends TestCase
{

    protected $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = app('jfuentestgn.cache.downloader');
    }

    public function testDummy()
    {
        $this->assertTrue(1 == 1);
    }

    public function testServiceInstanced()
    {
        $this->assertNotNull($this->service);
    }

    public function testServiceIsDownloader()
    {
        $this->assertTrue($this->service instanceof \JFuentesTgn\CacheUrlDownloader\UrlDownloader);
    }

    public function testServiceIsCacheDownloader()
    {
        $this->assertTrue($this->service instanceof \JFuentesTgn\CacheUrlDownloader\CacheUrlDownloader);
    }

    /**
     *
     */
    public function testUrlNotFound()
    {
        $url = 'http://www.google.es/xxxxx';
        $response = $this->service->downloadUrl($url);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @expectedException     Exception
     */
    public function testInvalidURL() {
        $url = 'httpx://www.domain.com/page.html';
        $this->service->downloadUrl($url);
    }


    public function testServiceWorks()
    {
        $url = 'http://www.example.com';
        Cache::flush();

        // 1. Download URL from network
        $resp = $this->service->downloadUrl($url);
        $this->assertEquals(200, $resp->getStatusCode());

        // 2. Check that content exists in cache
        $cacheId = CacheUrlDownloader::getUrlHash($url);
        $this->assertTrue(Cache::has($cacheId));

        // 3. Download URL from cache
        $resp2 = $this->service->downloadUrl($url);
        $this->assertEquals(200, $resp2->getStatusCode());

        // 4. Check that content from (3) == cached content
        $cached = Cache::get($cacheId);
        $this->assertEquals($cached, Psr7\str($resp2));
    }
}