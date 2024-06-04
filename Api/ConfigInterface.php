<?php

namespace CristianScheid\WeatherWidget\Api;

interface ConfigInterface
{
    public function isModuleEnabled(): bool;

    public function getWeatherParameters(): ?string;

    public function getTemperatureUnit(): string;

    public function getWindSpeedUnit(): string;

    public function getPrecipitationUnit(): string;
}
