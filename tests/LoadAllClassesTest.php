<?php

class LoadAllClassesTest extends \PHPUnit\Framework\TestCase
{
    /**
    * @doesNotPerformAssertions
    */
    public function test()
    {
        foreach (
        new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(
        'src/class'
        , RecursiveDirectoryIterator::FOLLOW_SYMLINKS
        )
        ) as $file)
            if ($file->isFile() && $file->getExtension() === 'php')
                require_once $file;
    }
}
