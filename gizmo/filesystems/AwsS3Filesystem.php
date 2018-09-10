<?php
namespace gizmo;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

class AwsS3Filesystemm {
    public function __construct($config) {
        $client = S3Client::factory([
            'credentials' => [
                'key'    => 'your-key',
                'secret' => 'your-secret',
            ],
            'region' => 'your-region',
            'version' => 'latest|version',
        ]);

        $adapter = new AwsS3Adapter($client, 'your-bucket-name', 'optional/path/prefix');

        $filesystem = new Filesystem($adapter);
    }
}
