<?php
namespace CristianScheid\WeatherWidget\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

class ConfigChangeObserver implements ObserverInterface
{
    protected $configWriter;
    protected $scopeConfig;
    protected $cacheTypeList;
    protected $cacheFrontendPool;

    public function __construct(
        WriterInterface $configWriter,
        ScopeConfigInterface $scopeConfig,
        TypeListInterface $cacheTypeList,
        Pool $cacheFrontendPool

    ) {
        $this->configWriter = $configWriter;
        $this->scopeConfig = $scopeConfig;
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
    }

    public function execute(Observer $observer)
    {
        $this->configWriter->save(
            'weather_report/general/last_config_change',
            date('Y-m-d H:i:s'),
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            0
        );

        $cacheTypeCode = 'block_html';
        $this->cacheTypeList->cleanType($cacheTypeCode);

        foreach ($this->cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }
}
