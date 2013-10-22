<?php

namespace Anh\Bundle\ContentBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Anh\Bundle\ContentBundle\Entity\Paper;
use Anh\Bundle\ContentBundle\AssetManager;

class AssetRemover implements EventSubscriber
{
    private $assetManager;

    public function __construct(AssetManager $assetManager)
    {
        $this->assetManager = $assetManager;
    }

    public function getSubscribedEvents()
    {
        return array(
            Events::preUpdate,
            Events::onFlush
        );
    }

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

        foreach ($diff as $fileName) {
            $this->assetManager->delete($fileName);
        }
    }

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
                $this->assetManager->delete($asset['fileName']);
            }
        }
    }
}
