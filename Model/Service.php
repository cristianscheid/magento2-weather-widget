<?php

namespace CristianScheid\WeatherWidget\Model;

use CristianScheid\WeatherWidget\Api\ConfigInterface;
use CristianScheid\WeatherWidget\Api\GeolocationInterface;
use CristianScheid\WeatherWidget\Api\ServiceInterface;
use CristianScheid\WeatherWidget\Api\WeatherInterface;

class Service implements ServiceInterface
{
    private WeatherInterface $weatherInterface;
    private GeolocationInterface $geolocationInterface;
    private ConfigInterface $configInterface;

    public function __construct(
        WeatherInterface     $weatherInterface,
        GeolocationInterface $geolocationInterface,
        ConfigInterface      $configInterface
    ) {
        $this->weatherInterface = $weatherInterface;
        $this->geolocationInterface = $geolocationInterface;
        $this->configInterface = $configInterface;
    }

    /**
     * Get weather data including module status, selected parameters, and weather data.
     *
     * @return string JSON encoded data containing module status, selected parameters, and weather data.
     */
    public function getData(): string
    {
        $selectedParameters = explode(',', $this->configInterface->getWeatherParameters());
        $location = $this->geolocationInterface->getLocation();
        $weatherData = null;
        if ($location) {
            $weatherData = $this->weatherInterface->getWeatherData($location);
        }
        $success = !is_null($selectedParameters) && !is_null($location) && !is_null($weatherData);

        $data = [
            'selectedParameters' => $selectedParameters,
            'weatherData' => $weatherData
        ];

        $response = [
            'success' => $success,
            'data' => $data
        ];

        return json_encode($response);
    }
}
