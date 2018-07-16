<?php

namespace Satellite\Twig;

use \Satellite\Routes\Router;

class RouterExtensions extends \Twig_Extension
{
    /**
     * @var \Satellite\Routes\Router
     */
    protected $router;

    /**
     * @param Router $router
     */
    public function __construct(Router $router)
    {

    }

    public function getName()
    {
        return "satellite_router";
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('path_for', array($this, 'pathFor')),
        ];
    }

    public function pathFor($path)
    {
        return "/sdfsdfsdfsdf";
    }
}
