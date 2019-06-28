<?php
namespace gizmo\filesystems;

use IteratorAggregate;
use ArrayIterator;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

use gizmo\ContentLeaf;
use gizmo\ContentNode;
use gizmo\ContentObject;
use gizmo\ContentRenderable;
use gizmo\Path;
use gizmo\util;

/**
 * Factory for ContentNodes for FlySystem stuff
 */
class AwsContentFactory
{
    public static function get(array $details, AwsS3Filesystem $fs)
    {
        switch($details['type'])
        {
            case 'dir':
                return new Dir($details, $fs);
            case 'file':
                return new File($details, $fs);
        }
    }
}

class AwsS3Filesystem implements ContentNode, IteratorAggregate
{
    public function __construct($config)
    {
        // var_dump($config);

        $this->config = $config;
        $client = new S3Client($config);
        $adapter = new AwsS3Adapter($client, $config['bucket'], $config['prefix']);
        $this->fs = new Filesystem($adapter);

        // Recursivly build up tree of FSObjects

        $this->contents = array();
        foreach ($this->fs->listContents($this->getPath()) as $key => $value) 
        {
            $this->contents[] = AwsContentFactory::get($value, $this);
        }
    }
    
    public function getIterator()
    {
        return new ArrayIterator($this->contents);
    }

    public function accept(ContentRenderable $renderable)
    {
        return $renderable->visitNode($this->root);
    }

    public function getPath() 
    {
        return new Path($this->config['prefix']);
    }

    public function getDirectUrl()
    {
        return '';
    }

    public function childCount()
    {
        return count($this->contents);
    }

    public function getCleanFilename()
    {
        return $this->root->getCleanFilename();
    }

    public function getExtension()
    {
        return '';
    } 

    public function read($path)
    {
        return $this->fs->read($path);
    }
}

abstract class FSObject implements ContentObject
{
    public function __construct(array $details, AwsS3Filesystem $fs)
    {
        $this->details = $details;
        $this->fs = $fs;
    }

    public function accept(ContentRenderable $renderable)
    {
        return $renderable->visitNode($this);
    }

    public function getPath(): Path
    {
        return new Path($this->details['path']);
    }

    public function childCount(): int
    {
        return count($this->contents);
    }

    public function getCleanFilename(): string
    {
        return '';
    } 

    public function getExtension(): string
    {
        $ext = pathinfo($this->getPath())['extension'];
        return $ext ? $ext : '';
    }
}


class Dir extends FSObject implements ContentNode, IteratorAggregate
{
    public function __construct(array $details, AwsS3Filesystem $fs)
    {
        parent::__construct($details, $fs);
        // Recursivly build a tree of FSObjects
        $this->contents = array();
        foreach ($this->fs->fs->listContents($this->getPath()) as $key => $value) 
        {
            //$this->contents[] = AwsContentFactory::get($value, $this->fs);
        }
    }

    public function getDirectUrl(): string
    {
        return "?p={$this->details['path']}";  // ???
    }

    public function getIterator()
    {
        return new ArrayIterator($this->contents);
    }

    public function getCleanFilename(): string
    {
        return $this->details['path'];
    } 
}

class File extends FSObject implements ContentLeaf
{
    public function __construct(array $details, AwsS3Filesystem $fs)
    {
        $this->details = $details;
        $this->fs = $fs;
    }

	/**
	 * URL to directly server the file using the webserver
	 * e.g. '/path/to/public_html/content/03_something.jpg' would be served as:
	 * 'www.example.com/content/03_something.jpg'
	 */
    public function getDirectUrl()
    {
        return "http://{$this->fs->config['bucket']}.s3.{$this->fs->config['region']}.amazonaws.com/{$this->details['path']}";
    }

    public function getFilename(): string
    {
        return $this->details['path'];
    }

    public function getExtension(): string
    {
        return $this->details['extension'];
    }

    public function getCleanFilename(): string
    {
        return $this->details['filename'];
    }

    public function accept(ContentRenderable $renderable)
	{
		return $renderable->visitLeaf($this);
    }
    
    public function getContents(): string
    {
        return $this->fs->read($this->getPath());
    }
}