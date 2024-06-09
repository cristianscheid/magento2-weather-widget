<?php

namespace CristianScheid\WeatherWidget\Model;

use CristianScheid\WeatherWidget\Api\WeatherInterface;
use CristianScheid\WeatherWidget\Api\GeolocationInterface;
use CristianScheid\WeatherWidget\Api\ConfigInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;

class Service
{
    private WeatherInterface $weatherInterface;
    private GeolocationInterface $geolocationInterface;
    private ConfigInterface $configInterface;
    private AssetRepository $assetRepository;

    public function __construct(
        WeatherInterface $weatherInterface,
        GeolocationInterface $geolocationInterface,
        ConfigInterface  $configInterface,
        AssetRepository $assetRepository
    ) {
        $this->weatherInterface = $weatherInterface;
        $this->geolocationInterface = $geolocationInterface;
        $this->configInterface = $configInterface;
        $this->assetRepository = $assetRepository;
    }

    /**
     * Get weather data including module status, selected parameters, weather data, and image base URL.
     *
     * @return string JSON encoded data containing module status, selected parameters, weather data, and image base URL.
     */
    public function getData()
    {
        $selectedParameters = explode(',', $this->configInterface->getWeatherParameters());

        $location = $this->geolocationInterface->getLocation();

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
