<?php

namespace Anh\ContentBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Anh\ContentBundle\Event\GenerateUrlEvent;
use Anh\ContentBundle\Entity\Paper;
use Anh\ContentBundle\Entity\Category;

class UrlGenerator implements EventSubscriberInterface
{
    public function __construct(array $sections)
    {
        $this->sections = $sections;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            GenerateUrlEvent::GENERATE_URL => array('onGenerateUrl', -200)
        );
    }

    public function onGenerateUrl(GenerateUrlEvent $event)
    {
        $data = $event->getData();

        if ($data instanceof Paper) {
            $arguments = array(
                'alias' => 'paper',
                'section' => $data->getSection(),
                'parameters' => $data->getUrlParameters()
            );
        } elseif ($data instanceof Category) {
            $arguments = array(
                'alias' => 'category',
                'section' => $data->getSection(),
                'parameters' => $data->getUrlParameters()
            );
        } elseif (
            is_array($data) &&
            isset($data['section']) && in_array($data['section'], $this->sections) &&
            isset($data['alias']) && in_array($data['alias'], array('papers', 'categories'))
        ) {
            $arguments = $data + array('parameters' => array());
        }

        if (isset($arguments)) {
            $event->setArguments($arguments);
            $event->stopPropagation();
        }
    }
}