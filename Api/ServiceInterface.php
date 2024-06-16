<?php

namespace CristianScheid\WeatherWidget\Api;

interface ServiceInterface
{
    /**
     * Get weather data including module status, selected parameters, and weather data.
     *
     * @return string JSON encoded data containing module status, selected parameters, and weather data.
     */
    public function getData(): string;
}
