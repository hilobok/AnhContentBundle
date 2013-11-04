<?php

namespace Anh\ContentBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;

use Anh\ContentBundle\AssetManager;

use Anh\MarkupBundle\Event\MarkupEvent;
use Anh\MarkupBundle\Event\MarkupCreateEvent;
use Anh\MarkupBundle\Event\MarkupParseEvent;
use Anh\MarkupBundle\Event\MarkupValidateEvent;

use Decoda\Decoda;
use Anh\ContentBundle\Decoda\Filter\PreviewFilter;
use Anh\ContentBundle\Decoda\Filter\AssetFilter;

class BbcodeParser implements EventSubscriberInterface
{
    /**
     * Sections config
     */
    protected $sections;

    /**
     * Router service
     */
    protected $router;

    /**
     * AssetManager service
     */
    protected $assetManager;

    public function __construct(array $sections, RouterInterface $router, AssetManager $assetManager)
    {
        $this->router = $router;
        $this->sections = $sections;
        $this->assetManager = $assetManager;
    }

    public static function getSubscribedEvents()
    {
        return array(
            MarkupEvent::CREATE => 'onCreate',
            MarkupEvent::PARSE => 'onParse',
            MarkupEvent::VALIDATE => 'onValidate'
        );
    }

    public function onCreate(MarkupCreateEvent $event)
    {
        if ($event->getType() != 'bbcode') {
            return;
        }

        $options = $event->getOptions();

        $decoda = new Decoda($event->getMarkup(), $options);
        $decoda->defaults();
        $decoda->addFilter(new \Decoda\Filter\TableFilter());

        // default image filter for section
        $options['filter'] = (isset($options['data']['section']) and
            isset($this->sections[$options['data']['section']]['filter'])) ?
                $this->sections[$options['data']['section']]['filter'] : ''
        ;

        $sectionRoute = (isset($options['data']['section']) and
            isset($this->sections[$options['data']['section']]['route'])) ?
                $this->sections[$options['data']['section']]['route'] : ''
        ;

        // generating proceed url
        if ($sectionRoute) {
            $route = $this->router->getRouteCollection()->get($sectionRoute);

            if (!$route) {
                throw new \InvalidArgumentException(sprintf("Unable to find route '%s'.", $sectionRoute));
            }

            $values = array_intersect_key(
                $options['data'],
                $route->getRequirements()
            );

            $options['url'] = $this->router->generate($sectionRoute, $values);
        }

        // assetManager for AssetFilter
        $options['assetManager'] = $this->assetManager;

        // add custom filters
        $decoda->addFilter(new AssetFilter($options));
        $decoda->addFilter(new PreviewFilter($options));

        // add custom decoda templates
        $decoda->getEngine()->addPath(__DIR__ . '/../Resources/decoda');

        $decoda->setConfig($options);
        $event->setOptions($options);
        $event->setParser($decoda);
    }

    public function onParse(MarkupParseEvent $event)
    {
        if ($event->getType() != 'bbcode') {
            return;
        }

        $decoda = $event->getParser();
        $decoda->reset($event->getMarkup());
        $decoda->setConfig($event->getOptions());
        $text = $decoda->parse();

        $event->setText($text);
    }

    public function onValidate(MarkupValidateEvent $event)
    {
        if ($event->getType() != 'bbcode') {
            return;
        }

        $decoda = $event->getParser();
        $decoda->parse();

        $errors = (array) $decoda->getErrors();

        $nesting = array();
        $closing = array();
        $scope = array();

        foreach ($errors as $error) {
            switch ($error['type']) {
                case Decoda::ERROR_NESTING:
                    $nesting[] = $error['tag'];
                    break;

                case Decoda::ERROR_CLOSING:
                    $closing[] = $error['tag'];
                    break;

                case Decoda::ERROR_SCOPE:
                    $scope[] = $error['child'] . ' in ' . $error['parent'];
                    break;
            }
        }

        $errors = array();

        if (!empty($nesting)) {
            $errors[] = sprintf('The following tags have been nested in the wrong order: %s', implode(', ', $nesting));
        }

        if (!empty($closing)) {
            $errors[] = sprintf('The following tags have no closing tag: %s', implode(', ', $closing));
        }

        if (!empty($scope)) {
            $errors[] = sprintf('The following tags can not be placed within a specific tag: %s', implode(', ', $scope));
        }

        $event->setErrors($errors);
    }
}
