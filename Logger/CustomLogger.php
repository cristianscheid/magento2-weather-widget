<?php

namespace CristianScheid\WeatherWidget\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class CustomLogger extends Logger
{

    private $fileName;

    public function __construct(
        $name = 'weather_widget',
        $fileName = 'cristian_scheid_weather_widget.log'
    ) {
        parent::__construct($name);
        $this->fileName = $fileName;
        $writer = new StreamHandler(BP . '/var/log/' . $this->fileName, Logger::DEBUG);
        $this->pushHandler($writer);
    }
}