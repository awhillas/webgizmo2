<?php
namespace gizmo;

class FileSystemFactory {
    public function getFileSystem($fs_type = 'local', $config = array()) {
        switch ($fs_type) {
            case 'local':
                return new gizmo\filesystem\LocalFilesystem($config);
            case 's3':
                return new gizmo\filesystem\AwsS3Filesystem($config);
            default:
                throw new gizmo\UnknownFilesystem();
        }
    }
}

class UnknownFilesystem extends Exception { }