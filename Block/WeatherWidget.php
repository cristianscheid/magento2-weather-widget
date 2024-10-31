<?php

namespace CristianScheid\WeatherWidget\Block;

use CristianScheid\WeatherWidget\Api\ConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class WeatherWidget extends Template
{
    /**
     * @var ConfigInterface
     */
    private ConfigInterface $configInterface;

    /**
     * @param ConfigInterface $configInterface
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        ConfigInterface $configInterface,
        Context         $context,
        array           $data = []
    ) {
        parent::__construct($context, $data);
        $this->configInterface = $configInterface;
    }

    /**
     * Check if the module is enabled
     *
     * @return bool
     */
    public function isModuleEnabled(): ?bool
    {
        return $this->configInterface->isModuleEnabled();
    }
    
    /**
     * Get last configuration change timestamp
     *
     * @return string
     */
    public function getLastConfigChange(): ?string
    {
        return $this->configInterface->getLastConfigChange();
    }
}
