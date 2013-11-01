<?php

namespace Anh\Bundle\ContentBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Anh\Bundle\ContentBundle\Entity\Paper;
use Anh\Bundle\ContentBundle\AssetManager;
use Oneup\UploaderBundle\Event\PostUploadEvent;

class AssetListener implements EventSubscriber
{
    /**
     * Asset manager
     *
     * @var AssetManager
     */
    private $assetManager;

    /**
     * Constructor
     *
     * @param AssetManager $assetManager
     */
    public function __construct(AssetManager $assetManager)
    {
        $this->assetManager = $assetManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::preUpdate,
            Events::onFlush
        );
    }

    /**
     * Sync assets for edited papers
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        if (!$args->getEntity() instanceof Paper or !$args->hasChangedField('assets')) {
            return;
        }

        $callback = function($value) {
            return $value['fileName'];
        };

        $newAssets = array_map($callback, (array) $args->getNewValue('assets'));
        $oldAssets = array_map($callback, (array) $args->getOldValue('assets'));

        $diff = array_diff($oldAssets, $newAssets);

        foreach ($diff as $asset) {
            $this->assetManager->remove($asset);
        }
    }

    /**
     * Deletes assets for deleted papers
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if (!$entity instanceof Paper) {
                continue;
            }

            // delete assets for deleted paper
            $assets = $entity->getAssets();

            foreach ($assets as $asset) {
                $this->assetManager->remove($asset['fileName']);
            }
        }
    }

    /**
     * Creates asset after file upload
     */
    public function onUpload(PostUploadEvent $event)
    {
        $response = $event->getResponse();

        if ($response->getSuccess()) {
            $response['asset'] = $this->assetManager->create($event->getFile());
        }
    }
}
