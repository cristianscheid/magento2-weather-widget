<?php

namespace CristianScheid\WeatherWidget\Block;

use CristianScheid\WeatherWidget\Api\ConfigInterface;
use CristianScheid\WeatherWidget\Model\Geolocation;
use CristianScheid\WeatherWidget\Model\Weather;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class WeatherWidget extends Template
{
    private ConfigInterface $configInterface;
    private Geolocation $geolocation;
    private Weather $weather;

    public function __construct(
        ConfigInterface  $configInterface,
        Geolocation $geolocation,
        Weather     $weather,
        Context     $context,
        array       $data = []
    ) {
        $this->configInterface = $configInterface;
        $this->geolocation = $geolocation;
        $this->weather = $weather;
        parent::__construct($context, $data);
    }

    public function isModuleEnabled()
    {
        return $this->configInterface->isModuleEnabled();
    }

    public function getWeatherData()
    {
        $location = $this->geolocation->getLocation();
        if (!$location) {
            return null;
        }
    
        return $this->weather->getWeatherData($location);
    }

    public function getSelectedParameters()
    {
        $selectedParameters = $this->configInterface->getWeatherParameters();
        return explode(',', $selectedParameters);
    }
}
