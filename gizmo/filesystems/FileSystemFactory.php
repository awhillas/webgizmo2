<?php
namespace gizmo\filesystems;

use Aws\S3\S3Client;
use League\Flysystem\Adapter\Local;
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
                    // throw new UnknownFilesystem("unknown file system given: type = ".$flyconfig['type']."? Valid types are 'local' or 's3'");
                    if (!array_key_exists('root', $config)) {
                        list($scriptPath) = get_included_files();
                        $config['root'] = dirname($scriptPath);
                    }
                    if (!array_key_exists('prefix', $config)) {
                        $config['prefix'] = '/content';
                    }
                    $adapter = new Local($config['root']);
                    $filesystem = new Filesystem($adapter);
            }
            return new FlySystemInterface($filesystem, $config['prefix']);
    }
}

class UnknownFilesystem extends Exception { }

/* Examples for each file system
    $fs_config = array(
        '/' => [
            'type' => 'local',
            'config' => [ 
                'root' => '',  // if not set assumes index.php's path
                'prefix' => '/content' 
            ]
        ],
        '/s3/content' => [
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
        '/dropbox/content' => [
            'type' => 'dropbox',
            'config' => [
                'authorizationToken' => '1234567890etc',
                'prefix' => 'optional/path/prefix'
            ]
        ]
    )
*/