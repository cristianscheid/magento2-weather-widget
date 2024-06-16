<?php

namespace CristianScheid\WeatherWidget\Block;

use CristianScheid\WeatherWidget\Api\ConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class WeatherWidget extends Template
{
    private ConfigInterface $configInterface;

    public function __construct(
        ConfigInterface $configInterface,
        Context         $context,
        array           $data = []
    ) {
        parent::__construct($context, $data);
        $this->configInterface = $configInterface;
    }

    public function isModuleEnabled(): ?bool
    {
        return $this->configInterface->isModuleEnabled();
    }

    public function getLastConfigChange(): ?string
    {
        return $this->configInterface->getLastConfigChange();
    }
}
