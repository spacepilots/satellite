<?php

namespace Satellite\Nodes\Interfaces;

interface Databag
{
    /**
     * @param string|null $key
     * @return mixed
     */
    public function getData();
}
