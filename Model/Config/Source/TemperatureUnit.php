<?php

namespace CristianScheid\WeatherWidget\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class TemperatureUnit implements ArrayInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'celsius', 'label' => __('Celsius °C')],
            ['value' => 'fahrenheit', 'label' => __('Fahrenheit °F')]
        ];
    }
}
