<?php

namespace Satellite;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Site implements \ArrayAccess
{
    protected $identifier;
    protected $data;

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
        $this->data = array_replace_recursive(
            Config::get('site', []),
            Config::get(['sites', $identifier])
        );

        // TODO: Determine this while routing.
        $this->data['languages']['current'] = 'en';
    }

    public function offsetUnset($key)
    {
        unset($this->data[$key]);
    }

    public function offsetSet($key, $node)
    {
        $this->data[$key] = $node;
    }

    public function offsetGet($key)
    {
        return $this->data[$key];
    }

    public function offsetExists($key)
    {
        return array_key_exists($key, $this->data);
    }
}
