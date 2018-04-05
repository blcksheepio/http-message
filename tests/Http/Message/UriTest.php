<?php

namespace Tests\Http\Message;

use BlcksheepIO\Http\Message\Uri;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Class UriTest
 * @package Tests\Http\Message
 */
class UriTest extends TestCase
{
    public function testWithSchemeReturnsNewUriInstanceWithNewScheme()
    {
        $uri = new Uri();
        $new = $uri->withScheme('http');
        self::assertNotSame($uri, $new);
        self::assertEquals('http', $new->getScheme());

        /*
         * This SHOULD be enabled once I have the FULL code
         * developed.
         *
         * TODO: Enable this
         */
        //self::assertEquals('http://user:pass@local.example.com:3001/foo?bar=baz#quz', (string) $new);
    }

    public function testReturnsSameInstanceIfWithSchemeIsIdentical()
    {
        $uri = new Uri();
        $new = $uri->withScheme('http');
        self::assertSame($new, $new->withScheme('http'));
    }

    public function testWithSchemeAcceptsEmptyString()
    {
        $uri = new Uri();
        $uri->withScheme('');
        self::assertEmpty($uri->getScheme());
    }

    /**
     * @return array
     */
    public function validSchemeDataProvider()
    {
        return [
            ['http', 'http'],
            ['Http', 'http'],
            ['HTTP', 'http'],
            ['https', 'https'],
            ['HttpS', 'https'],
            ['HTTPS', 'https'],
            ['http://', 'http'],
            ['Http://', 'http'],
            ['HTTP://', 'http'],
            ['https://', 'https'],
            ['HttpS://', 'https'],
            ['HTTPS://', 'https'],
        ];
    }

    /**
     * @param $scheme
     * @param $expected
     * @dataProvider validSchemeDataProvider
     */
    public function testWithSchemeConvertsSchemeToLowerCase($scheme, $expected)
    {
        $uri = new Uri();
        $new = $uri->withScheme($scheme);
        self::assertSame($expected, $new->getScheme());
    }

    /**
     * @param $scheme
     * @param $expected
     * @dataProvider validSchemeDataProvider
     */
    public function testWithSchemeSupportsHttpAndHttpsAndDoesNotRaiseException($scheme, $expected)
    {
        $uri = new Uri();
        $new = $uri->withScheme($scheme);
        self::assertEquals($expected, $new->getScheme());
    }

    /**
     * @param $scheme
     * @param $expected
     * @dataProvider validSchemeDataProvider
     */
    public function testWithSchemeRemovesExtraCharacters($scheme, $expected)
    {
        $uri = new Uri();
        $new = $uri->withScheme($scheme);
        self::assertEquals($expected, $new->getScheme());
    }

    public function testGetSchemeReturnsAnEmptyStringIfSchemeIsNotPresent()
    {
        $uri = new Uri();
        self::assertInternalType('string', $uri->getScheme());
        self::assertEmpty($uri->getScheme());
    }

    public function testWithSchemeThrowsExceptionIfInvalidSchemeIsRequested()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Unsupported scheme requested "ftp"; must be empty or in the set (http, https)');

        $uri = new Uri();
        $uri->withScheme('ftp');
    }

    /**
     * @return array
     */
    public function invalidPHPDataTypesForSchemeDataProvider()
    {
        return [
            [123],
            [true],
            [new stdClass()],
            [10.55],
            [['foo', 'bar']],
        ];
    }

    /**
     * @param $scheme
     * @dataProvider invalidPHPDataTypesForSchemeDataProvider
     */
    public function testWithSchemeThrowsExceptionIfInvalidDataTypeIsUsed($scheme)
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('BlcksheepIO\Http\Message\Uri::withScheme expects a string argument; received ' . ((is_object($scheme)) ? get_class($scheme) : gettype($scheme)));

        $uri = new Uri();
        $uri->withScheme($scheme);
    }
}
