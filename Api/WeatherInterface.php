<?php

namespace CristianScheid\WeatherWidget\Api;

interface WeatherInterface
{
    /**
     * Get weather data for the given location
     *
     * @param array $location
     * @return array
     */
    public function getWeatherData($location): array;
}
