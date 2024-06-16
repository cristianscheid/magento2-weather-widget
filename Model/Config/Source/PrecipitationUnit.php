<?php

namespace CristianScheid\WeatherWidget\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class PrecipitationUnit implements ArrayInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'mm', 'label' => __('Millimeter')],
            ['value' => 'inch', 'label' => __('Inch')]
        ];
    }
}
