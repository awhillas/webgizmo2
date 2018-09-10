<?php
namespace gizmo;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

class LocalFilesystem {
    public function __construct($config) {
        $adapter = new Local(__DIR__.'/content');
        $filesystem = new Filesystem($adapter);
    }
}
