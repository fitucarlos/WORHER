<?php

namespace App\Service\Utils;

use App\Service\Utils\HttpClientInterface;
use App\Model\Exception\HttpRequestException;
use Symfony\Contracts\HttpClient\HttpClientInterface as SymfonyHttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class HttpClient implements HttpClientInterface{
    private SymfonyHttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient){
        $this->httpClient = $httpClient;
    }

    public function request(string $method, string $url, array $options) : ResponseInterface{
        return $this->httpClient->request($method, $url, $options);
    }
}