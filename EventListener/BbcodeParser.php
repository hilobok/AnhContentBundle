<?php

namespace Anh\ContentBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Anh\ContentBundle\UrlGenerator;
use Anh\ContentBundle\AssetManager;

use Anh\MarkupBundle\Event\MarkupEvent;
use Anh\MarkupBundle\Event\MarkupCreateEvent;
use Anh\MarkupBundle\Event\MarkupParseEvent;
use Anh\MarkupBundle\Event\MarkupValidateEvent;
use Anh\MarkupBundle\Event\MarkupCommandEvent;

use Decoda\Decoda;
use Anh\ContentBundle\Decoda\Filter\PreviewFilter;
use Anh\ContentBundle\Decoda\Filter\AssetFilter;
use Anh\ContentBundle\Decoda\Filter\UrlFilter;
use Anh\ContentBundle\Decoda\Hook\ConvertBreaksHook;

class BbcodeParser implements EventSubscriberInterface
{
    /**
     * Sections config
     * @var array
     */
    protected $sections;

    /**
     * Url generator service
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * AssetManager service
     * @var AssetManager
     */
    protected $assetManager;

    /**
     * Constructor
     */
    public function __construct(array $sections, UrlGenerator $urlGenerator, AssetManager $assetManager)
    {
        $this->sections = $sections;
        $this->urlGenerator = $urlGenerator;
        $this->assetManager = $assetManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            MarkupEvent::CREATE => 'onCreate',
            MarkupEvent::PARSE => 'onParse',
            MarkupEvent::VALIDATE => 'onValidate',
            MarkupEvent::COMMAND => 'onCommand'
        );
    }

    /**
     * Creates and initializes bbcode markup parser
     *
     * @param MarkupCreateEvenet $event
     */
    public function onCreate(MarkupCreateEvent $event)
    {
        if ($event->getType() != 'bbcode') {
            return;
        }

        $options = $event->getOptions();

        $decoda = new Decoda($event->getMarkup(), $options);
        $decoda->defaults();
        $decoda->addFilter(new \Decoda\Filter\TableFilter());
        $decoda->addHook(new ConvertBreaksHook());

        // original url filter is to restrictive, replace it with custom
        $decoda->removeFilter('Url');
        $decoda->addFilter(new UrlFilter());

        // generate proceed url and add preview filter
        if (isset($options['data']['section']) && $this->sections[$options['data']['section']]['preview']) {
            $options['url'] = $this->urlGenerator->generateUrl(
                'paper',
                $options['data']['section'],
                $options['data']
            );
            $decoda->addFilter(new PreviewFilter($options));
        }

        // add asset manager and asset filter
        if (isset($options['data']['section']) && $this->sections[$options['data']['section']]['assets']) {
            // default filter for all assets without filter attribute
            $options['filter'] = isset($this->sections[$options['data']['section']]['filter']) ?
                $this->sections[$options['data']['section']]['filter'] : ''
            ;

            $options['assetManager'] = $this->assetManager;
            $decoda->addFilter(new AssetFilter($options));
        }

        // add custom decoda templates
        $decoda->getEngine()->addPath(__DIR__ . '/../Resources/decoda');

        $decoda->setConfig($options);
        $event->setOptions($options);
        $event->setParser($decoda);
    }

    /**
     * Parses bbcode markup
     *
     * @param MarkupParseEvent $event
     */
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

    /**
     * Validates bbcode markup
     *
     * @param MarkupValidateEvent
     */
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

    /**
     * Processes extra commands
     *
     * @param MarkupCommandEvent
     */
    public function onCommand(MarkupCommandEvent $event)
    {
        if ($event->getType() != 'bbcode') {
            return;
        }

        switch ($event->getCommand()) {
            case 'getTags':
                $tags = array();

                foreach ($event->getParser()->getFilters() as $filter) {
                    $tags = array_merge($tags, array_keys($filter->getTags()));
                }

                // leave only alphanumerical tags
                $tags = array_filter($tags, function($value) { return preg_match('/^[_a-z0-9]+$/', $value); });

                $event->setResult($tags);
                break;
        }
    }
}
