<?php

namespace CristianScheid\WeatherWidget\Observer;

use CristianScheid\WeatherWidget\Api\ConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

class ConfigChangeObserver implements ObserverInterface
{
    /**
     * @var ConfigInterface
     */
    private ConfigInterface $configInterface;

    /**
     * @var TypeListInterface
     */
    private TypeListInterface $cacheTypeList;

    /**
     * @var Pool
     */
    private Pool $cacheFrontendPool;

    /**
     * @param ConfigInterface $configInterface
     * @param TypeListInterface $cacheTypeList
     * @param Pool $cacheFrontendPool
     */
    public function __construct(
        ConfigInterface      $configInterface,
        TypeListInterface    $cacheTypeList,
        Pool                 $cacheFrontendPool
    ) {
        $this->configInterface = $configInterface;
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
    }

    /**
     * Handle configuration change events.
     *
     * @param Observer $observer
     * @return void
     */
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
