<?php

namespace CristianScheid\WeatherWidget\Model;

use CristianScheid\WeatherWidget\Api\ConfigInterface;
use CristianScheid\WeatherWidget\Logger\CustomLogger;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\HTTP\Client\Curl;

class Request
{
    private CacheInterface $cacheInterface;
    private Curl $curl;
    private ConfigInterface $configInterface;
    private CustomLogger $logger;

    public function __construct(
        CacheInterface  $cacheInterface,
        Curl            $curl,
        ConfigInterface $configInterface,
        CustomLogger    $logger
    ) {
        $this->cacheInterface = $cacheInterface;
        $this->curl = $curl;
        $this->configInterface = $configInterface;
        $this->logger = $logger;
    }

    public function makeRequestGeolocationApi($ipAddress): ?array
    {
        $cacheKey = 'geo_' . md5($ipAddress);
        $cacheLifetime = 1440 * 60;
        $cachedResponse = $this->cacheInterface->load($cacheKey);

        if ($cachedResponse) {
            return json_decode($cachedResponse, true);
        }

        try {
            $url = "http://ip-api.com/json/{$ipAddress}";
            $this->curl->get($url);
            $response = $this->curl->getBody();
            $responseDecoded = json_decode($response, true);

            if (isset($responseDecoded['status']) && $responseDecoded['status'] === 'fail') {
                $this->logger->info('API (ip-api.com) returned failure: ' . $responseDecoded['message'] . '. Attempting to retrieve location using real IP address.');
                $realIpAddress = file_get_contents("http://ipecho.net/plain");

                if ($realIpAddress) {
                    $url = "http://ip-api.com/json/{$realIpAddress}";

                    $this->curl->get($url);
                    $response = $this->curl->getBody();
                    $responseDecoded = json_decode($response, true);

                    if (isset($responseDecoded['status']) && $responseDecoded['status'] === 'fail') {
                        $this->logger->error('The second attempt with the real IP address also failed: ' . $responseDecoded['message']);
                        return null;
                    }
                } else {
                    $this->logger->error('Failed to obtain the real IP address from the external service.');
                    return null;
                }
            }

            if ($response) {
                $this->cacheInterface->save($response, $cacheKey, [], $cacheLifetime);
            }

            return $responseDecoded;
        } catch (\Exception $e) {
            $this->logger->error('makeRequestGeolocationApi() failed ' . $e->getMessage());
            return null;
        }
    }

    public function makeRequestWeatherApi($location, $selectedParameters): ?array
    {
        $cacheKey = 'weather_' . md5($location['lat'] . '_' . $location['lon'] . '_' . $selectedParameters);
        $cacheLifetime = 15 * 60; // 15 minutes

        $cachedResponse = $this->cacheInterface->load($cacheKey);
        if ($cachedResponse) {
            return json_decode($cachedResponse, true);
        }
        try {
            $latitude = $location['lat'];
            $longitude = $location['lon'];
            $url = "https://api.open-meteo.com/v1/forecast?latitude={$latitude}&longitude={$longitude}&current=";
            $url .= 'weather_code,is_day';

            if ($selectedParameters != '') {
                $unwantedParametersUrl = ["location", "location,", "weather_description", "weather_description,"];
                $parametersUrl = $selectedParameters;
                foreach ($unwantedParametersUrl as $param) {
                    $parametersUrl = str_replace($param, '', $parametersUrl);
                }
                $url .= ',' . $parametersUrl;
            }

            $selectedParameters = explode(',', $selectedParameters);

            if (in_array('temperature_2m', $selectedParameters)) {
                $temperatureUnit = $this->configInterface->getTemperatureUnit();
                switch ($temperatureUnit) {
                    case 'fahrenheit':
                        $url .= '&temperature_unit=fahrenheit';
                        break;
                    default:
                        break;
                }
            }
            if (in_array('precipitation', $selectedParameters)) {
                $precipitationUnit = $this->configInterface->getPrecipitationUnit();
                switch ($precipitationUnit) {
                    case 'inch':
                        $url .= '&precipitation_unit=inch';
                        break;
                    default:
                        break;
                }
            }
            if (in_array('wind_speed_10m', $selectedParameters)) {
                $windspeedUnit = $this->configInterface->getWindSpeedUnit();
                switch ($windspeedUnit) {
                    case 'ms':
                        $url .= '&wind_speed_unit=ms';
                        break;
                    case 'mph':
                        $url .= '&wind_speed_unit=mph';
                        break;
                    case 'kn':
                        $url .= '&wind_speed_unit=kn';
                        break;
                    default:
                        break;
                }
            }

            $url .= '&timezone=' . $location['timezone'];
            $this->curl->get($url);
            $response = $this->curl->getBody();
            $responseDecoded = json_decode($response, true);

            if (isset($responseDecoded['error']) && $responseDecoded['error'] === 'true') {
                $this->logger->error('API (api.open-meteo.com) returned failure: ' . $responseDecoded['reason']);
                return null;
            }

            if ($response) {
                $this->cacheInterface->save($response, $cacheKey, [], $cacheLifetime);
            }

            return $responseDecoded;
        } catch (\Exception $e) {
            $this->logger->error('makeRequestWeatherApi() failed: ' . $e->getMessage());
            return null;
        }
    }
}
