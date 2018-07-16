<?php

namespace Satellite\Routes;

class Route
{
    /**
     * @var string
     */
    protected $nodeId;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $site;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @param string $path
     * @return void
     */
    public function setPath(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     * @param string $nodeId
     * @return void
     */
    public function setNodeId(string $nodeId)
    {
        $this->nodeId = $nodeId;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $site
     * @return void
     */
    public function setSite(string $site)
    {
        $this->site = $site;
    }

    /**
     * @return string
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param string $locale
     * @return void
     */
    public function setLocale(string $locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }
}
