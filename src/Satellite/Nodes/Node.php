<?php

namespace Satellite\Nodes;

class Node
{
    protected $id;
    protected $name;
    protected $file;
    protected $parent;
    protected $data;
    protected $template;
    protected $orders = [];
    protected $locale;

    protected $variants = [];

    protected $flags = [];

    public function __construct(\SplFileInfo $file)
    {
        $this->id = spl_object_id($this);
        $this->file = $file;
    }

    public function getId()
    {
        return $this->id;
    }

    public function merge(Node $node)
    {
        $this->file = $node->getFile();
        $this->data = $node->getData();
        $this->template = $node->getTemplate();
        // $this->orders = $node->getOrders();
        $this->flags["routable"] = $node->getFlag("routable");
    }

    /**
     * @return \SplFileInfo
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getRealPath()
    {
        return $this->file->getRealPath();
    }

    public function getIdentifier()
    {
        return implode(".", array_filter([$this->name, $this->locale]));
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale;
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

    public function setOrder($collection, $order)
    {
        $this->orders[$collection] = $order;
    }

    public function getOrders()
    {
        return $this->orders;
    }

    public function addVariant(string $identifier, Node $node)
    {
        $this->variants[$identifier] = $node;
    }

    public function getVariants()
    {
        return $this->variants;
    }

    public function getVariant(string $identifier)
    {
        return isset($this->variants[$identifier])
            ? $this->variants[$identifier]
            : [];
    }

    public function hasVariants()
    {
        return !empty($this->variants);
    }

    public function hasVariant(string $identifier)
    {
        return isset($this->variants[$identifier]);
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setTemplate(string $template)
    {
        $this->template = $template;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setFlag(string $flag, $value)
    {
        $this->flags[$flag] = $value;
    }

    public function getFlag(string $flag)
    {
        return isset($this->flags[$flag]) ? $this->flags[$flag] : null;
    }
}
