<?php

namespace Anh\ContentBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class GenerateUrlEvent extends Event
{
    const GENERATE_URL = 'anh_content.generate_url';

    protected $data;
    protected $url;
    protected $arguments;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    public function getArguments()
    {
        return $this->arguments;
    }
}