<?php
namespace gizmo\filesystems;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;

use Exception;


class FileSystemFactory
{
    public static function get($fs_config = array())
    {
        foreach ($fs_config as $local_path => $flyconfig)
            $config = $flyconfig['config'];
            switch ($flyconfig['type']) {
                case 's3':
                    $client = new S3Client($config);
                    $adapter = new AwsS3Adapter($client, $config['bucket'], $config['prefix']);
                    $filesystem = new Filesystem($adapter);
                    break;
                case 'dropbox':
                    $client = new DropboxClient($config['authorizationToken']);
                    $adapter = new DropboxAdapter($client);
                    $filesystem = new Filesystem($adapter, ['case_sensitive' => false]);
                    break;
                case 'local':
                default:
                    // TODO: should default to local
                    // throw new UnknownFilesystem("unknown file system given: type = ".$flyconfig['type']."? Valid types are 'local' or 's3'");
                    $config = [ 'prefix' => '' ];
                    $adapter = new Local(__DIR__.'/content');
                    $filesystem = new Filesystem($adapter);
            }
            return new FlySystemInterface($filesystem, $config['prefix']);
    }
}

class UnknownFilesystem extends Exception { }

/* Examples for each file system
    $fs_config = array(
        '/path/to/map/to => [
            'type' => 'local',
            'config'=> [
                'root' => 'content'
            ]
        ],
        '/local/path/ie/content' => [
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
        ],
        '/local/dropbox/content' => [
            'type' => 'dropbox',
            'config' => [
                'authorizationToken' => '1234567890etc',
                'prefix' => 'optional/path/prefix'
            ]
        ]
    )
*/