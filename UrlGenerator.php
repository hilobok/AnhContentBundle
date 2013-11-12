<?php

namespace Anh\ContentBundle;

use Symfony\Component\Routing\RouterInterface;

class UrlGenerator
{
    /**
     * Holds sections configs
     * @var array
     */
    protected $sections;

    /**
     * Holds router service
     * @var RouterInterface
     */
    protected $router;

    /**
     * Constructor
     */
    public function __construct(array $sections, RouterInterface $router)
    {
        $this->sections = $sections;
        $this->router = $router;
    }

    /**
     * Generates url for content
     *
     * @param string $alias Route alias
     * @param string $section Section name
     * @param array $parameters Route parameters
     *
     * @return string Generated url
     */
    public function generateUrl($alias, $section, array $parameters = array())
    {
        if (!isset($this->sections[$section]['routes'][$alias])) {
            throw new \InvalidArgumentException(
                sprintf("Unable to find route '%s' in section '%s'.", $section, $alias)
            );
        }

        $routeName = $this->sections[$section]['routes'][$alias];

        return $this->router->generate(
            $routeName,
            $this->prepareParameters($routeName, $parameters)
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