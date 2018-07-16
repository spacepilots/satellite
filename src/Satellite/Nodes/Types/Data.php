<?php

namespace Satellite\Nodes\Types;

use Satellite\Nodes\Interfaces\Databag as DatabagInterface;
use Satellite\Nodes\Traits\Databag;
use SplFileInfo;
use Symfony\Component\Yaml\Yaml;

class Data extends AbstractFileNode implements DatabagInterface
{
    use Databag;

    /**
     * @param SplFileInfo $file
     */
    public function __construct(SplFileInfo $file)
    {
        parent::__construct($file);

        $this->setName(str_replace([".yaml", ".yml"], "", $this->name));

        $data = Yaml::parseFile($this->file->getRealPath());
        $this->data = array_key_exists('data', $data) ? $data['data'] : $data;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return "data";
    }

    public function mergeInto($node)
    {
        if ($this->hasData()) {
            $node->setData(\array_merge_recursive($node->getData(), $this->getData()));
        }
    }
}
