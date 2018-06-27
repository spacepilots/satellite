<?php

namespace Satellite\Twig;

class View extends \Slim\Views\Twig
{
    /**
     * Create new Twig view
     *
     * @param string|array $path     Path(s) to templates directory
     * @param array        $settings Twig environment settings
     */
    public function __construct($path, $settings = [])
    {
        $paths = is_string($path) ? [$path] : $path;

        $this->loader = new FilesystemLoader();
        foreach ($paths as $namespace => $path) {
            if (is_string($namespace)) {
                $this->loader->setPaths($path, $namespace);
            } else {
                $this->loader->addPath($path);
            }
        }

        $this->environment = new \Twig_Environment($this->loader, $settings);
    }
}
