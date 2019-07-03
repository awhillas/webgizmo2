<?php
namespace gizmo\filesystems;

use IteratorAggregate;
use ArrayIterator;

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
class ContentFactory
{
    public static function get(array $details, FlySystemInterface $fs, FileSystemSpecifics $specifics)
    {
        switch($details['type'])
        {
            case 'dir':
                return new Dir($details, $fs, $specifics);
            case 'file':
                return new File($details, $fs, $specifics);
        }
    }
}

class FlySystemInterface implements ContentNode, IteratorAggregate
{
    public function __construct(Filesystem $fs, string $root, FileSystemSpecifics $specifics)
    {
        $this->root = new Path($root);
        $this->fs = $fs;
        $this->specifics = $specifics;

        // Recursivly build up tree of FSObjects

        $this->contents = array();
        foreach ($this->fs->listContents($this->getPath()) as $key => $value) 
        {
                $this->contents[] = ContentFactory::get($value, $this, $specifics);
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
        return $this->root;
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

    // These functions are just interfaces to the Filesystem object

    public function read($path)
    {
        return $this->fs->read($path);
    }

    public function listContents($path)
    {
        return $this->fs->listContents($path);
    }
}

abstract class FSObject implements ContentObject
{
    public function __construct(array $details, FlySystemInterface $fs, FileSystemSpecifics $specifics)
    {
        $this->specifics = $specifics;
        $this->details = $details;
        $this->fs = $fs;
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
        return $this->getPath()->getExtension();
    }
}


class Dir extends FSObject implements ContentNode, IteratorAggregate
{
    public function __construct(array $details, FlySystemInterface $fs, FileSystemSpecifics $specifics)
    {
        parent::__construct($details, $fs, $specifics);
        // Recursivly build a tree of FSObjects
        $this->contents = array();
        foreach ($this->fs->listContents($this->getPath()) as $key => $value) 
        {
            $this->contents[] = ContentFactory::get($value, $this->fs, $specifics);
        }
    }

    public function __toString()
    {
        return "Dir: " . $this->getPath() . " ({$this->getPath()->getExtension()})";
    }

    public function accept(ContentRenderable $renderable)
    {
        return $renderable->visitNode($this);
    }

    public function getDirectUrl(): string
    {
        return "?p={$this->details['path']}";  // ???
    }

    /**
     * Iterate over the folders children.
     * Impliment the IteratorAggregate interface.
     */
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
    public function __toString()
    {
        return "File: " . $this->getFilename();
    }

    
    public function accept(ContentRenderable $renderable)
	{
		return $renderable->visitLeaf($this);
    }
    
	/**
	 * URL to directly server the file using the webserver
	 * e.g. '/path/to/public_html/content/03_something.jpg' would be served as:
	 * 'www.example.com/content/03_something.jpg'
     * TODO: this is S3 specific, needs to be passed into the constructor. Dependancy injecttion!
	 */
    public function getDirectUrl()
    {
        return $this->specifics->getDirectUrl($this);
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

    public function getContents(): string
    {
        return $this->fs->read($this->getPath());
    }
}