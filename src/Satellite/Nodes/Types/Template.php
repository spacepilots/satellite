<?php

namespace Satellite\Nodes\Types;

use Satellite\Nodes\Interfaces\Databag as DatabagInterface;
use Satellite\Nodes\Traits\Databag;
use Satellite\Nodes\Traits\Localized;
use SplFileInfo;
use Symfony\Component\Yaml\Yaml;

class Template extends AbstractFileNode implements DatabagInterface
{
    use Databag, Localized;

    /**
     * @param SplFileInfo $file
     */
    public function __construct(SplFileInfo $file)
    {
        parent::__construct($file);

        $content = file_get_contents($this->file->getRealPath());
        $parts = preg_split('/^[\s\r\n]?---[\s\r\n]?$/sm', PHP_EOL . ltrim($content));

        $this->setName(str_replace([".html.twig", ".twig"], "", $this->name));
        $this->setData(count($parts) < 3 ? [] : Yaml::parse(trim($parts[1])));
    }

    /**
     * @return string
     */
    public function getType()
    {
        return "template";
    }
}
