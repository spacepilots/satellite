<?php

namespace Satellite\Nodes\Types;

use Satellite\Nodes\Traits\Filesystem;
use SplFileInfo;

abstract class AbstractFileNode extends AbstractNode
{
    use Filesystem;

    public function __construct(SplFileInfo $file)
    {
        parent::__construct();

        $this->id = md5($file->getRealPath());
        $this->file = $file;
        $this->name = $file->getBasename();
    }
}
