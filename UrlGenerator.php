<?php

namespace Anh\ContentBundle;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouterInterface;
use Anh\ContentBundle\Event\GenerateUrlEvent;

class UrlGenerator
{
    /**
     * Content sections
     * @var array
     */
    protected $sections;

    /**
     * Router service
     * @var RouterInterface
     */
    protected $router;

    /**
     * Event dispatcher service
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Constructor
     */
    public function __construct(array $sections, RouterInterface $router, EventDispatcherInterface $dispatcher)
    {
        $this->sections = $sections;
        $this->router = $router;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Resolves from supplied data needed arguments for url generation and generates url
     *
     * @param mixed $data
     */
    public function resolveAndGenerate($data, $absolute = false)
    {
        $event = new GenerateUrlEvent($data);
        $this->dispatcher->dispatch(GenerateUrlEvent::GENERATE_URL, $event);

        if (!$event->isPropagationStopped()) {
            throw new \InvalidArgumentException(
                sprintf("Unable to generate url for '%s'.", is_object($data) ? get_class($data) : gettype($data))
            );
        }

        if ($event->getUrl()) {
            return $event->getUrl();
        }

        $arguments = $event->getArguments() + array(
            'alias' => '',
            'section' => '',
            'parameters' => ''
        );

        return $this->generateUrl(
            $arguments['alias'],
            $arguments['section'],
            $arguments['parameters'],
            $absolute
        );
    }

    public function generateUrl($alias, $section, $parameters, $absolute = false)
    {
        if (!isset($this->sections[$section]['routes'][$alias])) {
            throw new \InvalidArgumentException(
                sprintf("Unable to find route for '%s' in section '%s'.", $alias, $section)
            );
        }

        $routeName = $this->sections[$section]['routes'][$alias];

        return $this->router->generate(
            $routeName,
            $this->prepareParameters($routeName, $parameters),
            $absolute
        );
    }

    /**
     * Prepares route parameters leaving only required
     *
     * @param string $routeName
     * @param array $parameters
     *
     * @return array
     */
    protected function prepareParameters($routeName, array $parameters)
    {
        $route = $this->router->getRouteCollection()->get($routeName);

        if (!$route) {
            throw new \InvalidArgumentException(sprintf("Unable to find route '%s'.", $routeName));
        }

        return array_intersect_key(
            $parameters,
            $route->getRequirements()
        );
    }
}