<?php

namespace Satellite\Nodes\Types;

use Satellite\Nodes\Interfaces\Databag as DatabagInterface;
use Satellite\Nodes\Traits\Databag;

class Directory extends AbstractFileNode implements DatabagInterface
{
    use Databag;

    /**
     * @return string
     */
    public function getType()
    {
        return "directory";
    }
}
