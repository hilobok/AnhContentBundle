<?php

namespace Anh\Bundle\ContentBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;

use Anh\Bundle\ContentBundle\AssetManager;

use Anh\Bundle\MarkupBundle\Event\MarkupEvent;
use Anh\Bundle\MarkupBundle\Event\MarkupCreateEvent;

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
            MarkupEvent::CREATE => array('onCreate', -100)
        );
    }

    public function onCreate(MarkupCreateEvent $event)
    {
        if ($event->getType() != 'bbcode') {
            return;
        }

        $decoda = $event->getParser();
        $decoda->addFilter(new \Decoda\Filter\TableFilter());

        $options = $event->getOptions();

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
}
