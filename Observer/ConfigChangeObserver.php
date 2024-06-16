<?php

namespace CristianScheid\WeatherWidget\Observer;

use CristianScheid\WeatherWidget\Api\ConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

class ConfigChangeObserver implements ObserverInterface
{
    private ConfigInterface $configInterface;
    private TypeListInterface $cacheTypeList;
    private Pool $cacheFrontendPool;

    public function __construct(
        ConfigInterface      $configInterface,
        TypeListInterface    $cacheTypeList,
        Pool                 $cacheFrontendPool

    ) {
        $this->configInterface = $configInterface;
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
    }

    public function execute(Observer $observer): void
    {
        $this->configInterface->setLastConfigChange(date('Y-m-d H:i:s'));
        $cacheTypeCode = 'block_html';
        $this->cacheTypeList->cleanType($cacheTypeCode);
        foreach ($this->cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }
}
