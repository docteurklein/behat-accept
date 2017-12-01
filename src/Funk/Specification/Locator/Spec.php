<?php

namespace Funk\Specification\Locator;

use Behat\Testwork\Suite\Suite;
use Behat\Testwork\Specification\Locator\SpecificationLocator;
use Funk\Specification\Locator\Iterator;

class Spec implements SpecificationLocator
{
    private $basePath;

    public function __construct($basePath)
    {
        $this->basePath = $basePath;
    }

    public function getLocatorExamples()
    {
        return [
            'tests',
            'tests/a/class.php',
        ];
    }

    public function locateSpecifications(Suite $suite, $locator)
    {
        $iterator = $this->getFilesIterator($locator);

        return new Iterator($suite, $iterator, $this->basePath);
    }

    private function getFilesIterator($locator)
    {
        $path = $this->findAbsolutePath($locator);
        if (is_file($path)) {
            return new \ArrayIterator([new \SplFileInfo($path)]);
        }

        return new \RegexIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path)
            ), '/^.+\.php$/i',
            \RegexIterator::MATCH
        );
    }

    private function findAbsolutePath($path)
    {
        if (is_file($path) || is_dir($path)) {
            return realpath($path);
        }

        if (null === $this->basePath) {
            return false;
        }

        if (is_file($this->basePath . '/' . $path)
            || is_dir($this->basePath . '/' . $path)
        ) {
            return realpath($this->basePath . '/' . $path);
        }

        return false;
    }
}
