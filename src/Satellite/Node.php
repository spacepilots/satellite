<?php

namespace Satellite;

class Node
{
    protected $id;
    protected $path;
    protected $parent;
    protected $data;
    protected $template;

    protected $isRoutable = false;

    public function __construct(string $path)
    {
        $this->id = spl_object_hash($this);
        $this->path = $path;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function hasParent()
    {
        return $this->parent !== null;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function setTemplate(string $template)
    {
        $this->template = $template;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setRoutable($flag)
    {
        $this->isRoutable = $flag;
    }

    public function isRoutable()
    {
        return $this->isRoutable;
    }
}
