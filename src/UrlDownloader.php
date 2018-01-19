<?php

namespace JFuentesTgn\CacheUrlDownloader;

use Psr\Http\Message\ResponseInterface;

interface UrlDownloader
{
    public function downloadUrl(string $url, $options = []) : ResponseInterface;
}