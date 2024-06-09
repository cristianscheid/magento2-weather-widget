<?php

namespace CristianScheid\WeatherWidget\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Asset\Repository;

class ImageUrls extends Template
{
    protected $assetRepository;

    public function __construct(
        Context $context,
        Repository $assetRepository,
        array $data = []
    ) {
        $this->assetRepository = $assetRepository;
        parent::__construct($context, $data);
    }

    public function getWeatherIconUrl($imageName)
    {
        // Construct the path to the images directory
        $path = 'CristianScheid_WeatherWidget::images/weather-info/' . $imageName;

        // Generate the URL for the image using the Asset Repository
        return $this->assetRepository->getUrl($path);
    }
}
