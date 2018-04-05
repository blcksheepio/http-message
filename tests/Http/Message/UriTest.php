<?php

namespace Tests\Http\Message;

use BlcksheepIO\Http\Message\Uri;
use PHPUnit\Framework\TestCase;

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

    /**
     * @return array
     */
    public function mixedCaseSchemeDataProvider()
    {
        return [
            ['http'],
            ['Http'],
            ['HTTP'],
        ];
    }

    /**
     * @param $scheme
     * @dataProvider mixedCaseSchemeDataProvider
     */
    public function testWithSchemeConvertsSchemeToLowerCase($scheme)
    {
        $uri = new Uri();
        $new = $uri->withScheme($scheme);
        self::assertSame(strtolower($scheme), $new->getScheme());
    }

    public function testGetSchemeReturnsAnEmptyStringIfSchemeIsNotPresent()
    {
        $uri = new Uri();
        self::assertInternalType('string', $uri->getScheme());
        self::assertEmpty($uri->getScheme());
    }
}
