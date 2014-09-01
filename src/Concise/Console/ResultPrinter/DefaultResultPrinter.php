<?php

namespace Concise\Console\ResultPrinter;

use Concise\Console\Theme\DefaultTheme;
use Exception;
use PHPUnit_Framework_Test;
use PHPUnit_Runner_BaseTestRunner;

class DefaultResultPrinter extends AbstractResultPrinter
{
    protected $width;

    protected $theme;

    public function __construct($theme = null)
    {
        $this->width = exec('tput cols');
        if (!$theme) {
            $theme = new DefaultTheme();
        }
        $this->theme = $theme;
    }

    public function end()
    {
        $this->write("\n\n\n");
    }

    protected function add(PHPUnit_Framework_Test $test, $color, Exception $e = null)
    {
    }

    public function endTest($status, PHPUnit_Framework_Test $test, $time, Exception $e = null)
    {
        $colors = $this->theme->getTheme();
        if ($status === PHPUnit_Runner_BaseTestRunner::STATUS_FAILURE) {
            $this->add($test, $colors['failure'], $e);
        }
        $this->add($test, $colors['error'], $e);
    }
}
