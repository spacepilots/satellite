<?php

namespace Satellite\Nodes\Types;

use SplFileInfo;

class Controller extends AbstractFileNode
{
    /**
     * @param SplFileInfo $file
     */
    public function __construct(SplFileInfo $file)
    {
        parent::__construct($file);
        $this->setName(str_replace([".php"], "", $this->name));
    }

    /**
     * @return string
     */
    public function getType()
    {
        return "controller";
    }
}
