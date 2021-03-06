<?php

namespace SMW\Tests\Util\Mock;

use SMWQueryResult;
use SMWSQLStore3;
use SMWQuery;

/**
 * FIXME One would wish to have a FakeStore but instead SMWSQLStore3 is used in
 * order to avoid to implement all abstract methods specified by SMW\Store
 *
 * @group SMW
 * @group SMWExtension
 * @group medium
 * @group semantic-mediawiki-integration
 * @group mediawiki-databaseless
 *
 * @license GNU GPL v2+
 * @since   1.9.2
 *
 * @author mwjames
 */
class FakeQueryStore extends SMWSQLStore3 {

	protected $queryResult;

	public function setQueryResult( SMWQueryResult $queryResult ) {
		$this->queryResult = $queryResult;
	}

	public function getQueryResult( SMWQuery $query ) {
		return $this->queryResult;
	}
}
