<?php

namespace DoctrineExtensions\Sluggable;

/**
 * @Document(db="test_doctrine_ext_sluggable", collection="products")
 * @HasLifecycleCallbacks
 */
class SimpleProductFixture implements Sluggable
{

	/** @Id */
	protected $id;

	/** @String */
	protected $name;

	/** @Float */
	protected $price;

	/** @String */
	protected $weight;

	/** @String */
	protected $slug;

	/** @String */
	protected $slugDivider = '-';

	public function getId() {
		return $this->id;
	}

	public function setName($name) {
		$this->name = (string) $name;
		return $this;
	}

	public function getName() {
		return $this->name;
	}

	public function setWeight($weight) {
		$this->weight = (string) $weight;
		return $this;
	}

	public function getWeight() {
		return $this->weight;
	}

	public function setPrice($price) {
		$this->price = (float) $price;
		return $this;
	}

	public function getPrice() {
		return $this->price;
	}

	public function getSlug() {
		return $this->slug;
	}

	public function setSlugDivider($divider) {
		$this->slugDivider = (string) $divider;
		return $this;
	}

	public function getSlugDivider() {
		return $this->slugDivider;
	}

	public function getSlugFieldName() {
		return 'slug';
	}

	public function getSlugGeneratorFields() {
		return array('name', 'price', 'weight');
	}

}
