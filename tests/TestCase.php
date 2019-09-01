<?php

namespace App\Tests;

use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected $data_dir;

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    /**
     * @param string $directory
     * @return self
     */
    protected function setDataDirectory(string $directory = './data/'): self
    {
        $this->data_dir = $directory;
        return $this;
    }

    /**
     * @param string $file
     * @return string
     */
    public function getJsonFromFile($file): string
    {
        return file_get_contents("$this->data_dir/$file");
    }
}
