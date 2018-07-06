<?php
namespace gizmo;

/**
 * Find the ContentNode coresponding to a virtual path.
 */
class PathFinder
{
    function __construct(ContentObject $root_content_node) 
    {
        $this->root = $root_content_node;
        $this->vitual_schema = new VirtualPathSchema($root_content_node->getPath());
    }

    function find($target_vitual_path)
    {
        $Vpath = new Path($target_vitual_path, '/');
        return $this->_visit($Vpath, $this->root);
    }

    function _visit(Path $target_vitual_path, ContentObject $node) {
        // TODO: make this work!
        echo "target_vitual_path = $target_vitual_path <br>";
        echo "node = $node<br>";
        echo "root_content_node = $this->root<br>";
        
        // Base case, we have found the one we are looing for
        if ($node->childCount() === 0 && $target_vitual_path->length() === 0)
            return $node;
        // Check each child Node and take the first match
        foreach($node as $file_name => $sub_node)
            // Compare the first path component in the target to the current nodes head
            if ( $target_vitual_path->head() == $this->vitual_schema->translate($node->getPath()->tail()) )
                return $this->_visit($target_vitual_path->shift(), $sub_node);
        return false;
    }
}