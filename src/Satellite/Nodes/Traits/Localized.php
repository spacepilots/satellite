<?php

namespace Satellite\Nodes\Traits;

trait Localized
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @param string $locale
     * @return void
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }
}
