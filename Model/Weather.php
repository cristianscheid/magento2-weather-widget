<?php

namespace CristianScheid\WeatherWidget\Model;

use CristianScheid\WeatherWidget\Api\ConfigInterface;
use CristianScheid\WeatherWidget\Api\WeatherInterface;
use CristianScheid\WeatherWidget\Logger\CustomLogger;
use CristianScheid\WeatherWidget\Helper\WeatherUtils;

class Weather implements WeatherInterface
{
    /**
     * @var Request
     */
    private Request $request;

    /**
     * @var ConfigInterface
     */
    private ConfigInterface $configInterface;

    /**
     * @var WeatherUtils
     */
    private WeatherUtils $weatherUtils;

    /**
     * @var CustomLogger
     */
    private CustomLogger $logger;

    /**
     * @param Request $request
     * @param ConfigInterface $configInterface
     * @param WeatherUtils $weatherUtils
     * @param CustomLogger $logger
     */
    public function __construct(
        Request         $request,
        ConfigInterface $configInterface,
        WeatherUtils    $weatherUtils,
        CustomLogger    $logger
    ) {
        $this->request = $request;
        $this->configInterface = $configInterface;
        $this->weatherUtils = $weatherUtils;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function getWeatherData(array $location): ?array
    {
        try {
            $selectedParameters = $this->configInterface->getWeatherParameters();
            $response = $this->request->makeRequestWeatherApi($location, $selectedParameters);

            $weatherData = [];
            $weatherData['is_day'] = $response['current']['is_day'];
            $weatherCode = $response['current']['weather_code'];
            $weatherData['weather_icon'] = $this->weatherUtils->getWeatherIcon($weatherCode, $weatherData['is_day']);

            $selectedParameters = explode(',', $selectedParameters);
            foreach ($selectedParameters as &$parameter) {
                switch ($parameter) {
                    case 'location':
                        $weatherData[$parameter] = $location['city'] . ', '
                            . $location['region'] . ', '
                            . $location['country'];
                        break;
                    case 'weather_description':
                        $weatherData[$parameter] = $this->weatherUtils->getWeatherDescription($weatherCode);
                        break;
                    case 'temperature_2m':
                        $weatherData[$parameter] = $response['current'][$parameter];
                        $temperatureUnit = $this->configInterface->getTemperatureUnit();
                        $weatherData[$parameter] .= ' ' .
                            $this->weatherUtils->getTemperatureUnitLabel($temperatureUnit);
                        break;
                    case 'apparent_temperature':
                        $weatherData[$parameter] = $response['current'][$parameter];
                        $temperatureUnit = $this->configInterface->getTemperatureUnit();
                        $weatherData[$parameter] .= ' ' .
                            $this->weatherUtils->getTemperatureUnitLabel($temperatureUnit);
                        break;
                    case 'relative_humidity_2m':
                        $weatherData[$parameter] = $response['current'][$parameter];
                        $weatherData[$parameter] .= '%';
                        break;
                    case 'precipitation':
                        $weatherData[$parameter] = $response['current'][$parameter];
                        $precipitationUnit = $this->configInterface->getPrecipitationUnit();
                        $weatherData[$parameter] .= ' ' .
                            $this->weatherUtils->getPrecipitationUnitLabel($precipitationUnit);
                        break;
                    case 'wind_speed_10m':
                        $windspeedUnit = $this->configInterface->getWindSpeedUnit();
                        $weatherData[$parameter] = $response['current'][$parameter];
                        $weatherData[$parameter] .= ' ' .
                            $this->weatherUtils->getWindSpeedUnitLabel($windspeedUnit);
                        break;
                    case 'wind_direction_10m':
                        $weatherData[$parameter] = $response['current'][$parameter];
                        $weatherData[$parameter] .= '°';
                        break;
                    default:
                        break;
                }
            }
            return $weatherData;
        } catch (\Exception $e) {
            $this->logger->error('getWeatherData() failed: ' . $e->getMessage());
            return null;
        }
    }
}
