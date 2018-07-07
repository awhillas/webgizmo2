<?php
namespace gizmo;

/**
 * Encapsulate the business logic of the real-to-virtual path schema.
 * Should be independant of the underlying File schema and interchangable if we want to try something else in the future.
 * @TODO: make this an interface
 */
class VirtualPathSchema
{
    function __construct(Path $prefix = null)
    {
        $this->prefix = $prefix;
    }

    /**
     * Convert a real path to the virtual schema path
     */
    public function convert(Path $path)
    {
        $path = (!is_null($this->prefix))? $path->decapitate($this->prefix) : $path;

        $virtual_path = array();
        foreach($path as $part)
            array_push($virtual_path, self::toVirtual($part));
        
        return new Path($virtual_path);
    }

    /**
     * Logic to convert a part of a parth (i.e. file or directory name) to the virtual schema
     */
    public static function toVirtual($name)
    {
        // Remove the sorting number(s) prefix i.e. `69_`
        $virtual_name = preg_replace('/[0-9]{1,}_*/', '', $name);
        // Remove everything after the first dot '.'
        $virtual_name = preg_replace('/\..*$/', '', $virtual_name);
        return $virtual_name;
    }
}