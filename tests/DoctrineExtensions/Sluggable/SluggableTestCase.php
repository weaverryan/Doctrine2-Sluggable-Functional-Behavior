<?php

namespace DoctrineExtensions\Sluggable;

require '../../../../autoload.php';
require 'SimpleProductFixture.php';
//require 'SimpleProductFixture.php';

use Doctrine\Common\ClassLoader,
	Doctrine\Common\Annotations\AnnotationReader,
	Doctrine\Common\EventManager,
	Doctrine\ODM\MongoDB\Mongo,
	Doctrine\ODM\MongoDB\Configuration,
	Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver,
	Doctrine\ODM\MongoDB\Mapping\Driver\YamlDriver,
	Doctrine\ODM\MongoDB\DocumentManager;

class SluggableTestCase extends \PHPUnit_Framework_TestCase
{
	private $dm;
	private $em;
	private $devm;
	private $eevm;

	protected function setUp() {
		$configuration = new \Doctrine\ODM\MongoDB\Configuration();
		$reader = new \Doctrine\Common\Annotations\AnnotationReader();
		$reader->setDefaultAnnotationNamespace('Doctrine\ODM\MongoDB\Mapping\\');
		$configuration->setMetadataDriverImpl(
				new \Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver($reader)
				);
		$configuration->setProxyDir(__DIR__.'/../../example/cache/Proxies');
		$configuration->setProxyNamespace('Proxies');
		$this->dm = \Doctrine\ODM\MongoDB\DocumentManager::create(new Mongo(), $configuration);
		$this->devm = $this->dm->getEventManager();
	}

	protected function tearDown() {

	}

	public function getDocumentManager() {
		return $this->dm;
	}

	public function getDocumentEVM() {
		return $this->devm;
	}

	public function getEntityEvm() {
		return $this->eevm;
	}

}
