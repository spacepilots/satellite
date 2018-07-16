<?php

namespace Satellite\Nodes\Traits;

use SplFileInfo;

trait Filesystem
{
    /**
     * @var SplFileInfo
     */
    protected $file;

    /**
     * @param SplFileInfo $file
     * @return void
     */
    public function setFile(SplFileInfo $file)
    {
        $this->file = $file;
    }

    /**
     * @return SplFileInfo
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getRealPath()
    {
        return $this->file->getRealPath();
    }
}
