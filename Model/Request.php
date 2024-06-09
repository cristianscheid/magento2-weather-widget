<?php

namespace CristianScheid\WeatherWidget\Model;

use CristianScheid\WeatherWidget\Api\ConfigInterface;
use CristianScheid\WeatherWidget\Logger\CustomLogger;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\HTTP\Client\Curl;

class Request
{
    private CacheInterface $cache;
    private Curl $curl;
    private ConfigInterface $configInterface;
    private CustomLogger $logger;

    public function __construct(
        CacheInterface $cache,
        Curl $curl,
        ConfigInterface $configInterface,
        CustomLogger $logger
    ) {
        $this->cache = $cache;
        $this->curl = $curl;
        $this->configInterface = $configInterface;
        $this->logger = $logger;
    }

    public function makeRequestGeolocationApi($ipAddress) {

        $cacheKey = 'geo_' . md5($ipAddress);
        $cacheLifetime = 1440 * 60; // 1440 minutes (24 hours)

        $cachedData = $this->cache->load($cacheKey);
        if ($cachedData) {
            return json_decode($cachedData, true);
        }

        $url = "http://ip-api.com/json/{$ipAddress}";

        try {
            $this->curl->get($url);
            $response = $this->curl->getBody();
            $responseDecoded = json_decode($response, true);

            if (isset($responseDecoded['status']) && $responseDecoded['status'] === 'fail') {
                $this->logger->error('API (ip-api.com) returned failure: ' . $responseDecoded['message']);
                return null;
            }

            if ($response) {
                $this->cache->save($response, $cacheKey, [], $cacheLifetime);
            }

            return $responseDecoded;
            
        } catch (\Exception $e) {
            $this->logger->error('Error fetching data from API (ip-api.com): ' . $e->getMessage());
            return null;
        }
    }
    
    public function makeRequestWeatherApi($location, $selectedParameters)
    {
        $cacheKey = 'weather_' . md5($location['lat'] . '_' . $location['lon']);
        $cacheLifetime = 30 * 60; // 15 minutes

        $cachedResponse = $this->cache->load($cacheKey);
        if ($cachedResponse) {
            return $cachedResponse;
        } else {
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
            try {
                $this->curl->get($url);
                $response = $this->curl->getBody();
                $responseDecoded = json_decode($response, true);
    
                if (isset($responseDecoded['error']) && $responseDecoded['error'] === 'true') {
                    $this->logger->error('API (api.open-meteo.com) returned failure: ' . $responseDecoded['reason']);
                    return null;
                }
    
                if ($response) {
                    $this->cache->save($response, $cacheKey, [], $cacheLifetime);
                }

                return $response;
    
            } catch (\Exception $e) {
                $this->logger->error('Error fetching data from API (api.open-meteo.com): ' . $e->getMessage());
                return null;
            }
        }
    }
}
