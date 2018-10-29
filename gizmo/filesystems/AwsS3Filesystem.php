<?php
namespace gizmo\filesystems;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

class AwsS3Filesystemm
{
    public function __construct($config)
    {
        $client = S3Client::factory($config['credentials']);
        $adapter = new AwsS3Adapter($client, $config['bucket'], $config['prefix']);
        $filesystem = new Filesystem($adapter);
    }
}
