<?php

namespace CristianScheid\WeatherWidget\Model;

use CristianScheid\WeatherWidget\Api\ConfigInterface;
use CristianScheid\WeatherWidget\Api\GeolocationInterface;
use CristianScheid\WeatherWidget\Api\ServiceInterface;
use CristianScheid\WeatherWidget\Api\WeatherInterface;

class Service implements ServiceInterface
{
    /**
     * @var WeatherInterface
     */
    private WeatherInterface $weatherInterface;

    /**
     * @var GeolocationInterface
     */
    private GeolocationInterface $geolocationInterface;

    /**
     * @var ConfigInterface
     */
    private ConfigInterface $configInterface;

    /**
     * @param WeatherInterface $weatherInterface
     * @param GeolocationInterface $geolocationInterface
     * @param ConfigInterface $configInterface
     */
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
     * @inheritdoc
     */
    public function getData(): string
    {
        $selectedParameters = explode(',', $this->configInterface->getWeatherParameters());
        $location = $this->geolocationInterface->getLocation();
        $weatherData = null;
        if ($location) {
            $weatherData = $this->weatherInterface->getWeatherData($location);
        }
        $success = $selectedParameters !== null && $location !== null && $weatherData !== null;

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
