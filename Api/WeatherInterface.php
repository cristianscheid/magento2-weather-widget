<?php

namespace CristianScheid\WeatherWidget\Api;

interface WeatherInterface
{
    /**
     * Get weather data for the given location
     *
     * @param array $location
     * @return array|null
     */
    public function getWeatherData(array $location): ?array;
}
