<?php

namespace CristianScheid\WeatherWidget\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class WeatherParameters implements ArrayInterface
{
    /**
     * Retrieve an array of options for weather parameters.
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'location', 'label' => __('Location')],
            ['value' => 'weather_description', 'label' => __('Weather Description')],
            ['value' => 'temperature_2m', 'label' => __('Temperature')],
            ['value' => 'apparent_temperature', 'label' => __('Apparent Temperature')],
            ['value' => 'relative_humidity_2m', 'label' => __('Relative Humidity')],
            ['value' => 'precipitation', 'label' => __('Precipitation')],
            ['value' => 'wind_speed_10m', 'label' => __('Wind Speed')],
            ['value' => 'wind_direction_10m', 'label' => __('Wind Direction')],
        ];
    }
}
