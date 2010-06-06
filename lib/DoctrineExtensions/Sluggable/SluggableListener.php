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
    }

    /**
     * prePersist
     * 
     * @param EventArgs $e Event
     */
    public function prePersist(EventArgs $e)
    {
		if ($e instanceof ORMLifecycleEventArgs) {
			$entity = $e->getEntity();
			if ($entity instanceof Sluggable) {
		        $generator = new SlugGenerator($e->getEntityManager());
		        $generator->process($entity);
		    }
		} else if ($e instanceof ODMLifecycleEventArgs){
			$document = $e->getDocument();
			if ($document instanceof Sluggable) {
		        $generator = new SlugGenerator($e->getDocumentManager());
		        $generator->process($document);
		    }
		}
    }
}
