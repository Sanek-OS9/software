<?php
namespace Traits;

/**
 *
 */
trait Parse
{
    protected function functionName()
    {
        return 'test222';
    }
    protected function saveFile(string $url, string $file_path)
    {
        if (file_exists($path_file)) {
            return;
        }
        copy($screen, $path_file);
        return;
    }
}
