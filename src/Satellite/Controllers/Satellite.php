<?php

namespace Satellite\Controllers;

use Satellite\Nodes\Repository;
use Satellite\Routes\Router;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class Satellite
{
    /**
     * @var Repository
     */
    protected $nodes;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var \Slim\Views\Twig
     */
    protected $view;

    public function __construct(Container $container)
    {
        $this->nodes = $container->get("satellite.nodes");
        $this->router = $container->get("satellite.router");
        $this->view = $container->get("view");
    }

    public function displayDashboard(Request $request, Response $response)
    {
        return $this->view->render($response, "@base/pages/satellite/dashboard.html.twig", []);
    }

    public function displayNodes(Request $request, Response $response)
    {
        $nodes = $this->nodes->getNodes();

        $filter = [
            'sites' => ['any', 'default', 'workflows'],
            'types' => ['any', 'controller', 'data', 'file', 'directory', 'page'],
            'site' => !empty($request->getParam('site')) ? $request->getParam('site') : 'any',
            'type' => !empty($request->getParam('type')) ? $request->getParam('type') : 'any',
        ];

        $result = array_filter($nodes, function ($node) use ($filter) {
            return ($filter['site'] === "any" || $node->getSiteId() === $filter['site'])
                && ($filter['type'] === "any" || $node->getType() === $filter['type']);
        });

        return $this->view->render($response, "@base/pages/satellite/nodes.html.twig", [
            'nodes' => $result,
            'filter' => $filter,
        ]);
    }

    public function displayNode(Request $request, Response $response, $args)
    {
        return $this->view->render($response, "@base/pages/satellite/node.html.twig", [
            'nodes' => $this->nodes,
            'node' => $this->nodes[$args['id']],
        ]);
    }

    public function displayRoutes(Request $request, Response $response)
    {
        $filter = [
            'sites' => ['any', 'default', 'workflows'],
            'locales' => ['any', 'en', 'de'],
            'site' => !empty($request->getParam('site')) ? $request->getParam('site') : 'any',
            'locale' => !empty($request->getParam('locale')) ? $request->getParam('locale') : 'any',
        ];

        $this->router->compile($this->nodes, $filter['locale']);

        $result = array_filter($this->router->getRoutes(), function ($route) use ($filter) {
            return ($filter['site'] === "any" || $route->getSiteId() === $filter['site'])
                && ($filter['locale'] === "any" || $route->getLocale() === $filter['locale']);
        });

        return $this->view->render($response, "@base/pages/satellite/routes.html.twig", [
            'filter' => $filter,
            'routes' => $result,
            'nodes' => $this->nodes,
        ]);
    }
}
