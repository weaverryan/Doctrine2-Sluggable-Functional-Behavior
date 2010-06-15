<?php

namespace DoctrineExtensions\Sluggable;

use Doctrine\Common\EventArgs,
	Doctrine\ORM\Event\LifecycleEventArgs as ORMLifecycleEventArgs,
	Doctrine\ODM\MongoDB\Event\LifecycleEventArgs as ODMLifecycleEventArgs,
    Doctrine\Common\EventManager,
    Doctrine\ORM\Events;

class SluggableListener
{
    /**
     * Constructor
     *
     * @param EventManager $evm Event Manager
     */
    public function __construct(EventManager $evm)
    {
        $evm->addEventListener(Events::prePersist, $this);
        $evm->addEventListener(Events::preUpdate, $this);
    }

    /**
     * preUpdate
     *
     * @param EventArgs $e Event
     */
    public function preUpdate(EventArgs $e)
    {
        $sluggable = $this->getSluggable($e);

        if (isset($sluggable) && $sluggable->getSlug() === null) {
            $this->getGenerator($e)->process($sluggable);
            $changeset = $e->getDocumentChangeSet();
            $changeset[$sluggable->getSlugFieldName()] = array(null, $sluggable->getSlug());
        }
    }
    /**
     * prePersist
     *
     * @param EventArgs $e Event
     */
    public function prePersist(EventArgs $e)
    {
        $sluggable = $this->getSluggable($e);

        if (isset($sluggable)) {
            $this->getGenerator($e)->process($sluggable);
            $changeset = $e->getDocumentChangeSet();
            $changeset[$sluggable->getSlugFieldName()] = array(null, $sluggable->getSlug());
        }
    }

    protected function getSluggable(EventArgs $e) {
        // ORM Entities
        if ($e instanceof ORMLifecycleEventArgs) {
            $entity = $e->getEntity();
            if ($entity instanceof Sluggable) {
                return $entity;
            }
        }

        // ODM Documents
        if ($e instanceof ODMLifecycleEventArgs){
            $document = $e->getDocument();
            if ($document instanceof Sluggable) {
                return $document;
            }
        }

        return null;
    }

    protected function getGenerator(EventArgs $e) {
     	  if ($e instanceof ORMLifecycleEventArgs) {
            return new SlugGenerator($e->getEntityManager());
		  } else if ($e instanceof ODMLifecycleEventArgs){
            return new SlugGenerator($e->getDocumentManager());
        }
    }
}
