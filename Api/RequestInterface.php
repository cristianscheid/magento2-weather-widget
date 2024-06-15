<?php

namespace CristianScheid\WeatherWidget\Api;

interface RequestInterface
{
    /**
     * Make request to geolocation API
     *
     * @param string $ipAddress
     * @return array|null
     */
    public function makeRequestGeolocationApi($ipAddress);

    /**
     * Make request to weather API
     *
     * @param array $location
     * @param string $selectedParameters
     * @return array|null
     */
    public function makeRequestWeatherApi($location, $selectedParameters);
}
