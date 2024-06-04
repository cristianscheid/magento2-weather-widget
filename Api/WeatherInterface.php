<?php

namespace CristianScheid\WeatherWidget\Api;

interface WeatherInterface
{
    public function getWeatherData($location): array;
}
