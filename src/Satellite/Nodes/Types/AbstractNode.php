<?php

namespace Satellite\Nodes\Types;

use Satellite\Nodes\Interfaces\Databag;

abstract class AbstractNode
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $siteId;

    /**
     * @var string
     */
    protected $parentId;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $path;

    public function __construct()
    {
        $this->id = spl_object_hash($this);
    }

    /**
     * @return string
     */
    abstract public function getType();

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $siteId
     * @return void
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;
    }

    /**
     * @return string
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * @param string $parentId
     * @return void
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * @return string
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @return boolean
     */
    public function hasParentId()
    {
        return !empty($this->parentId);
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $path
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    public function rebase(AbstractNode $node)
    {
        $this->id = $node->getId();
        $this->name = $node->getName();
        $this->path = $node->getPath();
        $this->parentId = $node->getParentId();
        $this->siteId = $node->getSiteId();

        if ($node instanceof Databag) {
            $this->data = array_merge($this->data, $node->getData());
        }
    }
}
