<?php

namespace CristianScheid\WeatherWidget\Model;

use CristianScheid\WeatherWidget\Api\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Config implements ConfigInterface
{
    private const XML_PATH_ENABLED =            'weather_report/general/enabled';
    private const XML_PATH_WEATHER_PARAMETERS = 'weather_report/general/weather_parameters';
    private const XML_PATH_TEMPERATURE_UNIT =   'weather_report/general/temperature_unit';
    private const XML_PATH_WINDSPEED_UNIT =     'weather_report/general/windspeed_unit';
    private const XML_PATH_PRECIPITATION_UNIT = 'weather_report/general/precipitation_unit';
    private const XML_PATH_LAST_CONFIG_CHANGE = 'weather_report/general/last_config_change';
    

    private ScopeConfigInterface $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     */
    public function isModuleEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED);
    }

    /**
     * @inheritdoc
     */
    public function getWeatherParameters(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WEATHER_PARAMETERS);
    }
    
    /**
     * @inheritdoc
     */
    public function getTemperatureUnit(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TEMPERATURE_UNIT);
    }

    /**
     * @inheritdoc
     */
    public function getWindSpeedUnit(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WINDSPEED_UNIT);
    }

    /**
     * @inheritdoc
     */
    public function getPrecipitationUnit(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PRECIPITATION_UNIT);
    }

    /**
     * @inheritdoc
     */
    public function getLastConfigChange(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_LAST_CONFIG_CHANGE);
    }
}