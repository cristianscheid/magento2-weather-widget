<?php

namespace CristianScheid\WeatherWidget\Model;

use CristianScheid\WeatherWidget\Api\ConfigInterface;
use CristianScheid\WeatherWidget\Api\WeatherInterface;
use CristianScheid\WeatherWidget\Logger\CustomLogger;
use CristianScheid\WeatherWidget\Model\Request;

class Weather implements WeatherInterface
{
    private Request $request;
    private ConfigInterface $configInterface;
    private CustomLogger $logger;
    private $weatherCodeMapping = [
        0 => ['label' => 'Clear sky', 'icon' => 'clear.png'],
        1 => ['label' => 'Mainly clear', 'icon' => 'clear.png'],
        2 => ['label' => 'Partly cloudy', 'icon' => 'partial_cloud.png'],
        3 => ['label' => 'Overcast', 'icon' => 'overcast.png'],
        45 => ['label' => 'Fog', 'icon' => 'fog.png'],
        48 => ['label' => 'Depositing rime fog', 'icon' => 'fog.png'],
        51 => ['label' => 'Drizzle: Light intensity', 'icon' => 'rain.png'],
        53 => ['label' => 'Drizzle: Moderate intensity', 'icon' => 'rain.png'],
        55 => ['label' => 'Drizzle: Dense intensity', 'icon' => 'rain.png'],
        56 => ['label' => 'Freezing Drizzle: Light intensity', 'icon' => 'sleet.png'],
        57 => ['label' => 'Freezing Drizzle: Dense intensity', 'icon' => 'sleet.png'],
        61 => ['label' => 'Rain: Slight intensity', 'icon' => 'rain.png'],
        63 => ['label' => 'Rain: Moderate intensity', 'icon' => 'rain.png'],
        65 => ['label' => 'Rain: Heavy intensity', 'icon' => 'rain.png'],
        66 => ['label' => 'Freezing Rain: Light intensity', 'icon' => 'sleet.png'],
        67 => ['label' => 'Freezing Rain: Heavy intensity', 'icon' => 'sleet.png'],
        71 => ['label' => 'Snow fall: Slight intensity', 'icon' => 'snow.png'],
        73 => ['label' => 'Snow fall: Moderate intensity', 'icon' => 'snow.png'],
        75 => ['label' => 'Snow fall: Heavy intensity', 'icon' => 'snow.png'],
        77 => ['label' => 'Snow grains', 'icon' => 'snow.png'],
        80 => ['label' => 'Rain showers: Slight intensity', 'icon' => 'rain.png'],
        81 => ['label' => 'Rain showers: Moderate intensity', 'icon' => 'rain.png'],
        82 => ['label' => 'Rain showers: Violent intensity', 'icon' => 'rain.png'],
        85 => ['label' => 'Snow showers: Slight intensity', 'icon' => 'snow.png'],
        86 => ['label' => 'Snow showers: Heavy intensity', 'icon' => 'snow.png'],
        95 => ['label' => 'Thunderstorm: Slight or moderate', 'icon' => 'thunder.png'],
        96 => ['label' => 'Thunderstorm with slight hail', 'icon' => 'thunder.png'],
        99 => ['label' => 'Thunderstorm with heavy hail', 'icon' => 'thunder.png']
    ];

    public function __construct(
        Request $request,
        ConfigInterface $configInterface,
        CustomLogger $logger
    ) {
        $this->request = $request;
        $this->configInterface = $configInterface;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function getWeatherData($location): array
    {
        $selectedParameters = $this->configInterface->getWeatherParameters();
        $response = $this->request->makeRequestWeatherApi($location, $selectedParameters);
        $responseDecoded = json_decode($response, true);

        $weatherData = [];
        $weatherData['is_day'] =  $responseDecoded['current']['is_day'];
        $weatherCode = $responseDecoded['current']['weather_code'];
        $weatherData['weather_icon'] = $this->getWeatherIcon($weatherCode, $weatherData['is_day']);

        $selectedParameters = explode(',', $selectedParameters);
        foreach ($selectedParameters as &$parameter) {
            switch ($parameter) {
                case 'location':
                    $weatherData[$parameter] = $location['city'] . ', ' . $location['region'] . ', ' . $location['country'];
                    break;
                case 'weather_description':
                    $weatherData[$parameter] = $this->getWeatherDescription($weatherCode);
                    break;
                case 'temperature_2m':
                    $weatherData[$parameter] = $responseDecoded['current'][$parameter];
                    $temperatureUnit = $this->configInterface->getTemperatureUnit();
                    $weatherData[$parameter] .= ' ' . $this->getTemperatureUnitLabel($temperatureUnit);
                    break;
                case 'apparent_temperature':
                    $weatherData[$parameter] = $responseDecoded['current'][$parameter];
                    $temperatureUnit = $this->configInterface->getTemperatureUnit();
                    $weatherData[$parameter] .= ' ' . $this->getTemperatureUnitLabel($temperatureUnit);
                    break;
                case 'relative_humidity_2m':
                    $weatherData[$parameter] = $responseDecoded['current'][$parameter];
                    $weatherData[$parameter] .= '%';
                    break;
                case 'precipitation':
                    $weatherData[$parameter] = $responseDecoded['current'][$parameter];
                    $precipitationUnit = $this->configInterface->getPrecipitationUnit();
                    $weatherData[$parameter] .= ' ' . $this->getPrecipitationUnitLabel($precipitationUnit);
                    break;
                case 'wind_speed_10m':
                    $windspeedUnit = $this->configInterface->getWindSpeedUnit();
                    $weatherData[$parameter] = $responseDecoded['current'][$parameter];
                    $weatherData[$parameter] .= ' ' . $this->getWindSpeedUnitLabel($windspeedUnit);
                    break;
                case 'wind_direction_10m':
                    $weatherData[$parameter] = $responseDecoded['current'][$parameter];
                    $weatherData[$parameter] .= '°';
                    break;
                default:
                    break;
            }
        }
        return $weatherData;
    }

    private function getWeatherDescription($code): string
    {
        return $this->weatherCodeMapping[$code]['label'] ?? 'Unknown weather code';
    }

    private function getWeatherIcon($code, $isDay): string
    {
        if (in_array($code, ['0', '1', '2'])) {
            if ($isDay) {
                return 'day_' . $this->weatherCodeMapping[$code]['icon'] ?? 'unknown.png';
            } else {
                return 'night_' . $this->weatherCodeMapping[$code]['icon'] ?? 'unknown.png';
            }
        }
        return $this->weatherCodeMapping[$code]['icon'] ?? 'unknown.png';
    }

    private function getTemperatureUnitLabel($unit): string
    {
        switch ($unit) {
            case 'celsius':
                return '°C';
            case 'fahrenheit':
                return '°F';
            default:
                return '';
        }
    }

    private function getPrecipitationUnitLabel($unit): string
    {
        switch ($unit) {
            case 'mm':
                return 'mm';
            case 'inch':
                return 'inch';
            default:
                return '';
        }
    }

    private function getWindSpeedUnitLabel($unit): string
    {
        switch ($unit) {
            case 'kmh':
                return 'Km/h';
            case 'ms':
                return 'm/s';
            case 'mph':
                return 'Mph';
            case 'kn':
                return 'Knots';
            default:
                return '';
        }
    }
}
