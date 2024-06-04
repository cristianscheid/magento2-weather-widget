<?php

namespace CristianScheid\WeatherWidget\Api;

interface GeolocationInterface
{
    public function getLocation(): ?array;
}