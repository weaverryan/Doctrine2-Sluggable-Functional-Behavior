<?php

namespace DoctrineExtensions\Sluggable;

/**
 * @Document(db="test_doctrine_ext_sluggable", collection="test")
 */
class TestDocument implements Sluggable {
	/** @Id */
	public $id;

	/** @String */
	public $slug;

    /**
     * @inheritDoc
     */
    function getSlugFieldName() {
		return 'slug';
	}

    /**
     * @inheritDoc
     */
    function getSlug() {
		return $this->slug;
	}

    /**
     * @inheritDoc
     */
    function getSlugGeneratorFields() {
		return array('slug');
	}

}