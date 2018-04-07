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
    public function testConstructorCorrectlySetsProperties()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        self::assertSame('https', $uri->getScheme());
        self::assertSame('user:pass', $uri->getUserInfo());
        self::assertSame('local.example.com', $uri->getHost());
        self::assertSame(3001, $uri->getPort());
    }

    public function testWithSchemeReturnsNewUriInstanceWhenNewScheme()
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

    public function testWithSchemeReturnsSameInstanceIfWhenSchemeIsIdentical()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withScheme('http');
        self::assertNotSame($uri, $new);
        self::assertSame('http', $new->getScheme());
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
    public function invalidPHPDataTypesDataProvider()
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
     * @dataProvider invalidPHPDataTypesDataProvider
     */
    public function testWithSchemeThrowsExceptionWhenInvalidDataTypeIsUsed($scheme)
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('BlcksheepIO\Http\Message\Uri::withScheme expects a string argument; received ' . ((is_object($scheme)) ? get_class($scheme) : gettype($scheme)));
        $uri = new Uri();
        $uri->withScheme($scheme);
    }

    /**
     * @return array
     */
    public function validUserDataProvider()
    {
        return [
            ['foo', 'bar'],
            ['foo', ''],
        ];
    }

    /**
     * @param $user
     * @param $password
     * @dataProvider validUserDataProvider
     */
    public function testWithUserInfoReturnsNewInstanceWhenNewUser($user, $password)
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withUserInfo($user, $password);
        self::assertNotSame($uri, $new);
        $userInfo = (!empty($password)) ? $user . ':' . $password : $user;
        self::assertSame($userInfo, $new->getUserInfo());
    }

    public function testWithUserInfoReturnsSameInstanceWhenUserAndPasswordAreIdentical()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withUserInfo('user', 'pass');
        self::assertSame($uri, $new);
        self::assertSame('user:pass', $new->getUserInfo());
    }

    /**
     * @param $user
     * @dataProvider invalidPHPDataTypesDataProvider
     */
    public function testWithUserInfoThrowsExceptionWhenUserIsInvalidDataType($user)
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('BlcksheepIO\Http\Message\Uri::withUserInfo expects a string argument; received ' . ((is_object($user)) ? get_class($user) : gettype($user)));
        $uri = new Uri();
        $uri->withUserInfo($user);
    }

    /**
     * @param $user
     * @dataProvider invalidPHPDataTypesDataProvider
     */
    public function testWithUserInfoThrowsExceptionWhenPasswordIsInvalidDataType($password)
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('BlcksheepIO\Http\Message\Uri::withUserInfo expects a string argument; received ' . ((is_object($password)) ? get_class($password) : gettype($password)));
        $uri = new Uri();
        $uri->withUserInfo('foo', $password);
    }

    public function userInfoProviderDataProvider()
    {
        return [
            // name       => [ user,              credential, expected ]
            'valid-chars' => ['foo', 'bar', 'foo:bar'],
            'colon'       => ['foo:bar', 'baz:bat', 'foo%3Abar:baz%3Abat'],
            'at'          => ['user@example.com', 'cred@foo', 'user%40example.com:cred%40foo'],
            'percent'     => ['%25', '%25', '%25:%25'],
            'invalid-enc' => ['%ZZ', '%GG', '%25ZZ:%25GG'],
        ];
    }

    /**
     * @param $user
     * @param $password
     * @param $expected
     * @dataProvider userInfoProviderDataProvider
     */
    public function testWithUserCorrectlyEncodesUserNameAndPassword($user, $password, $expected)
    {
        $uri = new Uri();
        $new = $uri->withUserInfo($user, $password);
        self::assertSame($new->getUserInfo(), $expected);
    }

    public function testWithHostReturnsNewInstanceWhenNewHost()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withHost('foo.com');
        self::assertNotSame($uri, $new);
        self::assertSame('foo.com', $new->getHost());
    }

    public function testWithHostReturnsSameInstanceWhenHostIsIdentical()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withHost('local.example.com');
        self::assertSame($uri, $new);
        self::assertSame('local.example.com', $new->getHost());
    }

    public function testWithHostAcceptsEmptyString()
    {
        $uri = new Uri();
        $new = $uri->withHost('');
        self::assertSame($uri, $new);
        self::assertEmpty($new->getHost());
    }

    public function testWithHostConvertsHostToLowerCase()
    {
        $uri = new Uri();
        $new = $uri->withHost('FOO.COM');
        self::assertSame('foo.com', $new->getHost());
    }

    /**
     * @param $host
     * @dataProvider invalidPHPDataTypesDataProvider
     */
    public function testWithHostThrowsExceptionWhenInvalidDataTypeIsUsed($host)
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('BlcksheepIO\Http\Message\Uri::withHost expects a string argument; received ' . ((is_object($host)) ? get_class($host) : gettype($host)));
        $uri = new Uri();
        $uri->withHost($host);
    }

    public function testWithPortReturnsSameInstanceWhenPortIsIdentical()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withPort(80);
        self::assertNotSame($uri, $new);
        self::assertSame(80, $new->getPort());
    }

    public function testWithPortReturnsSameInstanceIfWhenPortIsIdentical()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withPort(3001);
        self::assertSame($uri, $new);
        self::assertEquals(3001, $new->getPort());
    }

    public function validPortDataTypesDataProvider()
    {
        return [
            'integer' => [80],
            'string'  => ['80'],
            'null'    => [null],
        ];
    }

    /**
     * @param $port
     * @dataProvider validPortDataTypesDataProvider
     */
    public function testWithPortAcceptsValidDataTypes($port)
    {
        $uri = new Uri();
        $new = $uri->withPort($port);
        self::assertEquals($port, $new->getPort());
    }

    public function invalidPortDataTypesDataProvider()
    {
        return [
            'true'      => [true],
            'false'     => [false],
            'string'    => ['string'],
            'array'     => [[3000]],
            'object'    => [(object)[3000]],
            'zero'      => [0],
            'too-small' => [-1],
            'too-big'   => [65536],
        ];
    }

    /**
     * @param $port
     * @dataProvider invalidPortDataTypesDataProvider
     */
    public function testWithPortThrowsExceptionWhenInvalidDataTypeIsUsed($port)
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Invalid port');
        $uri = new Uri();
        $uri->withPort($port);
    }

    public function testWithPortThrowsExceptionIfPortIsOutOfRange()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Invalid port "0" specified; must be a valid TCP/UDP port');
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withPort(0);
        self::expectExceptionMessage('Invalid port "65536" specified; must be a valid TCP/UDP port');
        $new = $uri->withPort(65536);
    }
}
