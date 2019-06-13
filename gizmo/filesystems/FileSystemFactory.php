<?php
namespace gizmo\filesystems;

use Exception;

/*
    $fs_config = array(
        '/path/to/map/to => [
            'type' => 'local',
            'config'=> [
                'root' => 'content'
            ]
        ],
        '/local/path/ie/content' => {
            'type' => 's3',
            'config' => [
                'bucket' => 'your-bucket-name',
                'prefix' => 'optional/path/prefix',
                'credentials' => [
                    'credentials' => [
                        'key'    => 'your-key',
                        'secret' => 'your-secret',
                    ],
                    'region' => 'your-region',
                    'version' => 'latest|version',
                ]
            ]
        }
    )
*/
class FileSystemFactory
{
    public static function get($fs_config = array())
    {
        foreach ($fs_config as $local_path => $config)
            switch ($config['type']) {
                case 'local':
                    return new LocalFileSystem($config);
                case 's3':
                    return new AwsS3Filesystem($config);
                default:
                    throw new UnknownFilesystem("unknown file system given: type = ".$config['type']."? Valid types are 'local' or 's3'");
            }
    }
}

class UnknownFilesystem extends Exception { }