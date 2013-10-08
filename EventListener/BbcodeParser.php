<?php

namespace Anh\Bundle\ContentBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;

use Anh\Bundle\ContentBundle\AssetManager;

use Anh\Bundle\MarkupBundle\Event\MarkupEvent;
use Anh\Bundle\MarkupBundle\Event\MarkupCreateEvent;
use Anh\Bundle\MarkupBundle\Event\MarkupParseEvent;
use Anh\Bundle\MarkupBundle\Event\MarkupValidateEvent;

use Decoda\Decoda;
use Anh\Bundle\ContentBundle\Decoda\Filter\PreviewFilter;
use Anh\Bundle\ContentBundle\Decoda\Filter\AssetFilter;

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

        // generating url
        if (!empty($options['previewOnly'])) {
            $section = $options['extra']['section'];

            if (empty($this->sections[$section])) {
                throw new \InvalidArgumentException(sprintf("Section '%s' not configured.", $section));
            }

            $section = $this->sections[$section];
            $route = $this->router->getRouteCollection()->get($section['route']);

            if (!$route) {
                throw new \InvalidArgumentException(sprintf("Unable to find route '%s'.", $section['route']));
            }

            $values = array_intersect_key(
                $options['extra'],
                $route->getRequirements()
            );

            $options['url'] = $this->router->generate($section['route'], $values);
        }

        $options['path'] = $this->assetManager->getPath('uploads');

        $engine = $decoda->getEngine();
        $engine->addPath(__DIR__ . '/../Resources/decoda');

        $decoda->setConfig($options);

        $decoda->addFilter(new AssetFilter($options));
        $decoda->addFilter(new PreviewFilter($options));

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
