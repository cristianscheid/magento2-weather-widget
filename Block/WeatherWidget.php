<?php

namespace CristianScheid\WeatherWidget\Block;

use CristianScheid\WeatherWidget\Api\ConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class WeatherWidget extends Template
{
    private ConfigInterface $configInterface;

    public function __construct(
        ConfigInterface  $configInterface,
        Context     $context,
        array       $data = []
    ) {
        $this->configInterface = $configInterface;
        parent::__construct($context, $data);
    }

    public function getLastConfigChange()
    {
        return $this->configInterface->getLastConfigChange();
    }
}