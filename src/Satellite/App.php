<?php

namespace Satellite;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class App
{
    protected $rootPath;
    protected $app;

    public static function initialize(string $rootPath)
    {
        Config::load(realpath($rootPath . "/config.php"), true);
        return new self($rootPath);
    }

    public function __construct(string $rootPath)
    {
        $this->rootPath = $rootPath;
        $this->systemPath = realpath(__DIR__ . '/../../');

        $this->app = new \Slim\App(Config::get('system'));

        $this->findNodes();
        $this->findRoutes();

        $satellite = $this;
        $container = $this->app->getContainer();

        $container['view'] = function ($c) use ($satellite) {
            $view = new \Satellite\Twig\View([
                '__main__' => $satellite->rootPath,
                'base' => $satellite->systemPath . '/base',
            ], [
                'debug' => Config::get('env', false),
                'cache' => Config::get(['cache', 'path'])
            ]);

            // Instantiate and add Slim specific extension
            // $router = $c->get('router');
            // $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
            // $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

            return $view;
        };

        // $container['notFoundHandler'] = function ($c) {
        //     return function ($request, $response) use ($c) {
        //         return $c['view']->render($response->withStatus(404), 'pages/404.html.twig');
        //     };
        // };

        $this->app->get('/[{path:.*}]', function (Request $request, Response $response, $args) use ($satellite) {
            list($siteIdentifier, $nodeId) = $satellite->router->match($request);

            if (!$nodeId) {
                throw new \Slim\Exception\NotFoundException($request, $response);
            }

            $node = $satellite->nodes[$nodeId];
            $path = str_replace($satellite->rootPath, '', $node->getPath());
            $site = new Site($siteIdentifier);

            return $this->view->render($response, $path, [
                'site' => $site
            ]);
        });
    }

    public function run()
    {
        $this->app->run();
    }

    protected function findNodes()
    {
        $pagePath = realpath($this->rootPath . '/pages');

        if (!$pagePath) {
            throw new \RuntimeException("Missing pages/ folder");
        }

        $nodes = [];
        $parent = null;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($pagePath),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        while($iterator->valid()) {
            if (!$iterator->isDot()) {
                $node = new Node($iterator->getRealPath());
                $node->setParent($parent);
                $node->setRoutable($iterator->isFile());

                if ($iterator->isDir()) {
                    $parent = $node->getId();
                }

                $nodes[$node->getId()] = $node;
            }
            $iterator->next();
        }

        $this->nodes = new Nodes($nodes);
        $this->nodes->parseAll();
    }

    protected function findRoutes()
    {
        $this->router = new Router();
        $this->router->refresh($this->nodes);
    }
}
