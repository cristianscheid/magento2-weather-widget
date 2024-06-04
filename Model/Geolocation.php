<?php

namespace CristianScheid\WeatherWidget\Model;

use CristianScheid\WeatherWidget\Api\GeolocationInterface;
use CristianScheid\WeatherWidget\Logger\CustomLogger;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;

class Geolocation implements GeolocationInterface
{
    private RemoteAddress $remoteAddress;
    private Curl $curl;
    private CustomLogger $logger;

    public function __construct(
        RemoteAddress $remoteAddress,
        Curl $curl,
        CustomLogger $logger
    ) {
        $this->remoteAddress = $remoteAddress;
        $this->curl = $curl;
        $this->logger = $logger;
    }

    public function getLocation(): ?array
    {
        $visitorIpAddress = $this->remoteAddress->getRemoteAddress();
        $url = "http://ip-api.com/json/{$visitorIpAddress}";

        try {
            $this->curl->get($url);
            $response = $this->curl->getBody();
            $responseDecoded = json_decode($response, true);

            if (isset($responseDecoded['status']) && $responseDecoded['status'] === 'fail') {
                $this->logger->error('API (ip-api.com) returned failure: ' . $responseDecoded['message']);
                return null;
            }

            return $responseDecoded;
            
        } catch (\Exception $e) {
            $this->logger->error('Error fetching data from API (ip-api.com): ' . $e->getMessage());
            return null;
        }
    }
}
