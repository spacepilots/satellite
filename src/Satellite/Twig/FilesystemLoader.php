<?php

namespace Satellite\Twig;

class FilesystemLoader extends \Twig_Loader_Filesystem
{
    public function getSourceContext($name)
    {
        $path = $this->findTemplate($name);

        $content = file_get_contents($path);
        $pattern = '/^[\s\r\n]?---[\s\r\n]?$/sm';
        $parts = preg_split($pattern, PHP_EOL . ltrim($content));

        if (count($parts) === 3) {
            $content = implode(PHP_EOL . '---' . PHP_EOL, array_slice($parts, 2));
        }

        return new \Twig_Source($content, $name, $path);
    }
}
