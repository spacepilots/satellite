<?php

namespace Satellite;

use \Slim\Http\Request;

class Router
{
    protected $routes = [];

    public function __construct() {}

    public function getRoutes()
    {
        return $this->routes;
    }

    public function refresh(Nodes $nodes)
    {
        foreach ($nodes->getNodes() as $node) {
            if (!$node->isRoutable()) {
                continue;
            }
            $this->routes[$this->getPath($nodes, $node)] = $node->getId();
        }
    }

    public function match(Request $request)
    {
        $path = "/default" . $request->getUri()->getPath();

        if (array_key_exists($path, $this->routes)) {
            return $this->routes[$path];
        }
        if (array_key_exists($path . 'index', $this->routes)) {
            return $this->routes[$path . 'index'];
        }

        return null;
    }

    protected function getPath(Nodes $nodes, Node $node)
    {
        $path = [];
        $pointer = $node;

        do {
            $segments = explode(".", basename($pointer->getPath()));
            $path[] = reset($segments);
            $pointer = $nodes->getParent($pointer);
        } while ($pointer !== null);

        return "/" . implode("/", array_reverse($path));
    }
}
