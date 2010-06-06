<?php

namespace DoctrineExtensions\Sluggable;

require 'SluggableTestCase.php';

use	Doctrine\Common\EventManager,
		Doctrine\ORM\Events,
		DoctrineExtensions\Sluggable\SluggableListener,
		Bundle\ProductBundle\Documents\SimpleProduct;

class FunctionalTest extends SluggableTestCase
{

	private $productDoc;
	private $productEnt;

	private $documents = array(
			'Bundle\ProductBundle\Documents\SimpleProduct',
			);	

	private $entities = array(
			'Bundle\ProductBundle\Entities\SimpleProduct',
			);		

	protected function setUp() {
		parent::setUp();
		$devm = $this->getDocumentEvm();
		$listener = new SluggableListener($devm);

		$this->productDoc = new SimpleProduct();
	}

	protected function tearDown() {
		parent::tearDown();
		$this->productDoc = null;
				
		foreach ($this->documents as $document) {
			$this->getDocumentManager()->getDocumentCollection($document)->drop();
		}
	  
		/*
		 $this->productEnt = null;
		 foreach ($this->entities as $entity) {
			 $this->getEntityManager()->getEntityCollection($entity)->drop();
		 }
	  */
	}

	public function testProductDocInit() {	
		$this->assertNull($this->productDoc->getName());
		$this->assertNull($this->productDoc->getWeight());
		$testName   = 'Test Name';
		$testWeight = '5 kg';
		$testPrice  = 100.00;
		$this->productDoc->setName($testName);
		$this->productDoc->setWeight($testWeight);
		$this->productDoc->setPrice($testPrice);
		$dm = $this->getDocumentManager();
		$dm->persist($this->productDoc);
		$dm->flush();

		$dm->detach($this->productDoc);

		$productRepo = $dm->getRepository('Bundle\ProductBundle\Documents\SimpleProduct');
		$productNull = $productRepo->find('xxx');	
		$this->assertNotEquals(get_class($productNull),'Bundle\ProductBundle\Documents\SimpleProduct');
		$product = $productRepo->find($this->productDoc->getId());	
		$this->assertEquals(get_class($product),'Bundle\ProductBundle\Documents\SimpleProduct');
		$this->assertEquals($testName, $product->getName());
		$this->assertEquals($testWeight, $product->getWeight());
		$this->assertEquals($testPrice, $product->getPrice());
	}

	public function testInstanceOfSluggable() {
		$doc = new \ReflectionClass('Bundle\ProductBundle\Documents\SimpleProduct');
		$sluggable = new \ReflectionClass('DoctrineExtensions\Sluggable\Sluggable');
		$slugField = $this->productDoc->getSlugFieldName();
		$slugGeneratorFields = $this->productDoc->getSlugGeneratorFields();
		$this->assertEquals($slugField, 'slug');
		$this->assertEquals($slugGeneratorFields, array('name', 'price', 'weight'));
	}

	public function testSlugGeneratorWithFloat() {
		$testName   = 'My Name With Spaces';
		$testWeight = '10 kg';
		$testPrice  = 54.67;
		$this->productDoc->setName($testName);
		$this->productDoc->setWeight($testWeight);
		$this->productDoc->setPrice($testPrice);
		
		$expectedSlug = 'my-name-with-spaces-54-67-10-kg';
		
		$dm = $this->getDocumentManager();
		$dm->persist($this->productDoc);
		$dm->flush();
		
		$dm->detach($this->productDoc);
		
		$productRepo = $dm->getRepository('Bundle\ProductBundle\Documents\SimpleProduct');
		$product = $productRepo->find($this->productDoc->getId());	
		$this->assertEquals($product->getSlug(),$expectedSlug);
	}	

	public function testSlugGeneratorWithStandardNonalphanumerics() {
		$testName    = '~Mott`s, !Apple #Sauce $Green%Yellow ^not &red ';
		$testName   .= '*30 (of) -1 _per +pound =viscous {liquid} [delicious]';
		$testWeight  = '|other \groceries: carrot; "peas\' <spinach> cucumber, ';
		$testWeight .= 'Strawberry. Raisin? Done/';
		$testPrice   = 100.00;
		$this->productDoc->setName($testName);
		$this->productDoc->setWeight($testWeight);
		$this->productDoc->setPrice($testPrice);
		
		$expectedSlug  = 'mott-s-apple-sauce-green-yellow-not-red-30-of-1-per-pound-viscous-liquid-delicious';
		$expectedSlug .= '-';
		$expectedSlug .= '100';
		$expectedSlug .= '-';
		$expectedSlug .= 'other-groceries-carrot-peas-spinach-cucumber-strawberry-raisin-done';
		
		$dm = $this->getDocumentManager();
		$dm->persist($this->productDoc);
		$dm->flush();
		
		$dm->detach($this->productDoc);
		
		$productRepo = $dm->getRepository('Bundle\ProductBundle\Documents\SimpleProduct');
		$product = $productRepo->find($this->productDoc->getId());	
		$this->assertEquals($product->getSlug(),$expectedSlug);
	}

	public function testSlugGeneratorSetDivider() {
		$divider     = '/';
		$testName    = '~Mott`s, !Apple #Sauce $Green%Yellow ^not &red ';
		$testName   .= '*30 (of) -1 _per +pound =viscous {liquid} [delicious]';
		$testWeight  = '|other \groceries: carrot; "peas\' <spinach> cucumber, ';
		$testWeight .= 'Strawberry. Raisin? Done/';
		$testPrice   = 100.00;
		$this->productDoc->setName($testName);
		$this->productDoc->setWeight($testWeight);
		$this->productDoc->setPrice($testPrice);
		$this->productDoc->setSlugDivider($divider);
		
		$expectedSlug  = 'mott/s/apple/sauce/green/yellow/not/red/30/of/1/per/pound/viscous/liquid/delicious';
		$expectedSlug .= '/';
		$expectedSlug .= '100';
		$expectedSlug .= '/';
		$expectedSlug .= 'other/groceries/carrot/peas/spinach/cucumber/strawberry/raisin/done';
		
		$dm = $this->getDocumentManager();
		$dm->persist($this->productDoc);
		$dm->flush();
		
		$dm->detach($this->productDoc);
		
		$productRepo = $dm->getRepository('Bundle\ProductBundle\Documents\SimpleProduct');
		$product = $productRepo->find($this->productDoc->getId());	
		$this->assertEquals($product->getSlug(),$expectedSlug);
	}

}
