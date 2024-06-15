<?php

namespace CristianScheid\WeatherWidget\Api;

interface ConfigInterface
{
    /**
     * Check if the module is enabled
     *
     * @return bool
     */
    public function isModuleEnabled(): bool;

    /**
     * Get the weather parameters
     *
     * @return string|null
     */
    public function getWeatherParameters(): ?string;

    /**
     * Get the temperature unit
     *
     * @return string
     */
    public function getTemperatureUnit(): string;

    /**
     * Get the wind speed unit
     *
     * @return string
     */
    public function getWindSpeedUnit(): string;

    /**
     * Get the precipitation unit
     *
     * @return string
     */
    public function getPrecipitationUnit(): string;

    /**
     * Get last config change
     *
     * @return string
     */
    public function getLastConfigChange(): string;
}
