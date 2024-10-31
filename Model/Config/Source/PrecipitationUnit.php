<?php

namespace CristianScheid\WeatherWidget\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class PrecipitationUnit implements ArrayInterface
{
    /**
     * Retrieve an array of options for precipitation units.
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'mm', 'label' => __('Millimeter')],
            ['value' => 'inch', 'label' => __('Inch')]
        ];
    }
}
