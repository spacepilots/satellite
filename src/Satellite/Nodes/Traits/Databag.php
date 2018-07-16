<?php

namespace Satellite\Nodes\Traits;

trait Databag
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param [] $data
     * @return void
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @param string|null $key
     * @return boolean
     */
    public function hasData($key = null)
    {
        return empty($key)
        ? !empty($this->data)
        : array_key_exists($key, $this->data);
    }

    /**
     * @param string|null $key
     * @return mixed
     */
    public function getData($key = null, $default = null)
    {
        if (!empty($key)) {
            return array_key_exists($key, $this->data)
            ? $this->data[$key]
            : $default;
        }
        return $this->data;
    }
}
