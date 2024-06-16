<?php

namespace CristianScheid\WeatherWidget\Model;

use CristianScheid\WeatherWidget\Api\GeolocationInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;

class Geolocation implements GeolocationInterface
{
    private RemoteAddress $remoteAddress;
    private Request $request;

    public function __construct(
        RemoteAddress $remoteAddress,
        Request       $request
    ) {
        $this->remoteAddress = $remoteAddress;
        $this->request = $request;
    }

    public function getLocation() : ?array
    {
        $visitorIpAddress = $this->remoteAddress->getRemoteAddress();
        return $this->request->makeRequestGeolocationApi($visitorIpAddress);
    }
}
