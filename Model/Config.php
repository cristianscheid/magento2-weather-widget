<?php

namespace CristianScheid\WeatherWidget\Model;

use CristianScheid\WeatherWidget\Api\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;

class Config implements ConfigInterface
{
    private const XML_PATH_ENABLED =            'weather_widget/general/enabled';
    private const XML_PATH_WEATHER_PARAMETERS = 'weather_widget/general/weather_parameters';
    private const XML_PATH_TEMPERATURE_UNIT =   'weather_widget/general/temperature_unit';
    private const XML_PATH_WINDSPEED_UNIT =     'weather_widget/general/windspeed_unit';
    private const XML_PATH_PRECIPITATION_UNIT = 'weather_widget/general/precipitation_unit';
    private const XML_PATH_LAST_CONFIG_CHANGE = 'weather_widget/general/last_config_change';

    private ScopeConfigInterface $scopeConfig;
    private WriterInterface $configWriter;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        WriterInterface      $configWriter
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
    }

    /**
     * @inheritdoc
     */
    public function isModuleEnabled(): ?bool
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
    public function getTemperatureUnit(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TEMPERATURE_UNIT);
    }

    /**
     * @inheritdoc
     */
    public function getWindSpeedUnit(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WINDSPEED_UNIT);
    }

    /**
     * @inheritdoc
     */
    public function getPrecipitationUnit(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PRECIPITATION_UNIT);
    }

    /**
     * @inheritdoc
     */
    public function getLastConfigChange(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_LAST_CONFIG_CHANGE);
    }

    /**
     * @inheritdoc
     */
    public function setLastConfigChange(string $value): void
    {
        $this->configWriter->save(self::XML_PATH_LAST_CONFIG_CHANGE, $value);
    }
}