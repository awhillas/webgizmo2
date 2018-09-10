<?php
namespace gizmo;

/**
 * Find the ContentNode coresponding to a virtual path.
 */
class PathFinder
{
    function __construct(ContentObject $root_content_node)
    {
        $this->root_node = $root_content_node;
        $this->vitual_schema = new VirtualPathSchema($this->root_node->getPath());
    }

    function find($target_vitual_path)
    {
        $Vpath = new Path($target_vitual_path, '/');
        return $this->_visit($Vpath, $this->root_node);
    }

    function _visit(Path $target_vitual_path, ContentObject $node) {
        // Base case, we have found the one we are looking for
        if ($this->vitual_schema->convert($node->getPath())->equals($target_vitual_path))
            return $node;

        // Check each child Node and take the first match
        foreach($node as $file_name => $sub_node)
        {
            $sub_node_path = $sub_node->getPath();
            $sub_vpath = $this->vitual_schema->convert($sub_node_path);
            if($target_vitual_path->hasPrefix($sub_vpath))
                return $this->_visit($target_vitual_path, $sub_node);
        }
        return false;
    }
}
