<?php

namespace SMW\Tests\SPARQLStore;

use SMW\SPARQLStore\BadHttpResponseMapper;

/**
 * @covers \SMW\SPARQLStore\BadHttpResponseMapper
 *
 * @ingroup Test
 *
 * @group SMW
 * @group SMWExtension
 *
 * @license GNU GPL v2+
 * @since 2.0
 *
 * @author mwjames
 */
class BadHttpResponseMapperTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$httpRequest = $this->getMockBuilder( '\SMW\HttpRequest' )
			->disableOriginalConstructor()
			->getMock();

		$this->assertInstanceOf(
			'\SMW\SPARQLStore\BadHttpResponseMapper',
			new BadHttpResponseMapper( $httpRequest )
		);
	}

	/**
	 * @dataProvider curlErrorCodeThatNotThrowsExceptionProvider
	 */
	public function testResponseToHttpRequestThatNotThrowsException( $curlErrorCode ) {

		$httpRequest = $this->getMockBuilder( '\SMW\HttpRequest' )
			->disableOriginalConstructor()
			->getMock();

		$httpRequest->expects( $this->once() )
			->method( 'getLastErrorCode' )
			->will( $this->returnValue( $curlErrorCode ) );

		$instance = new BadHttpResponseMapper( $httpRequest );
		$instance->mapResponseToHttpRequest( 'Foo', 'Bar' );
	}

	public function testResponseToHttpRequestForInvalidErrorCodeThrowsException() {

		$httpRequest = $this->getMockBuilder( '\SMW\HttpRequest' )
			->disableOriginalConstructor()
			->getMock();

		$httpRequest->expects( $this->once() )
			->method( 'getLastErrorCode' )
			->will( $this->returnValue( 99999 ) );

		$instance = new BadHttpResponseMapper( $httpRequest );

		$this->setExpectedException( '\Exception' );
		$instance->mapResponseToHttpRequest( 'Foo', 'Bar' );
	}

	/**
	 * @dataProvider httpCodeThatThrowsExceptionProvider
	 */
	public function testResponseToHttpRequesForHttpErrorThatThrowsException( $httpErrorCode ) {

		// PHP doesn't know CURLE_HTTP_RETURNED_ERROR therefore using 22
		// http://curl.haxx.se/libcurl/c/libcurl-errors.html

		$httpRequest = $this->getMockBuilder( '\SMW\HttpRequest' )
			->disableOriginalConstructor()
			->getMock();

		$httpRequest->expects( $this->once() )
			->method( 'getLastErrorCode' )
			->will( $this->returnValue( 22 ) );

		$httpRequest->expects( $this->once() )
			->method( 'getInfo' )
			->with( $this->equalTo( CURLINFO_HTTP_CODE ) )
			->will( $this->returnValue( $httpErrorCode ) );

		$instance = new BadHttpResponseMapper( $httpRequest );

		$this->setExpectedException( '\SMW\SPARQLStore\BadHttpDatabaseResponseException' );
		$instance->mapResponseToHttpRequest( 'Foo', 'Bar' );
	}

	public function testResponseToHttpRequesForHttpErrorThatNotThrowsException() {

		$httpRequest = $this->getMockBuilder( '\SMW\HttpRequest' )
			->disableOriginalConstructor()
			->getMock();

		$httpRequest->expects( $this->once() )
			->method( 'getLastErrorCode' )
			->will( $this->returnValue( 22 ) );

		$httpRequest->expects( $this->once() )
			->method( 'getInfo' )
			->with( $this->equalTo( CURLINFO_HTTP_CODE ) )
			->will( $this->returnValue( 404 ) );

		$instance = new BadHttpResponseMapper( $httpRequest );
		$instance->mapResponseToHttpRequest( 'Foo', 'Bar' );
	}

	public function curlErrorCodeThatNotThrowsExceptionProvider() {

		$provider = array(
			array( CURLE_GOT_NOTHING ),
			array( CURLE_COULDNT_CONNECT )
		);

		return $provider;
	}

	public function httpCodeThatThrowsExceptionProvider() {

		$provider = array(
			array( 400 ),
			array( 500 )
		);

		return $provider;
	}
}
