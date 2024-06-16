<?php

namespace CristianScheid\WeatherWidget\Api;

interface GeolocationInterface
{
    /**
     * Get the location based on visitor's IP address
     *
     * @return array|null
     */
    public function getLocation() : ?array;
}
