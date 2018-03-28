<?php

namespace Tests\Http\Message;

use BlcksheepIO\Http\Message\Uri;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;
use stdClass;

/**
 * Class UriTest
 * @package Tests\Http\Message
 */
class UriTest extends TestCase
{
    /**
     * @var UriInterface $uri
     */
    protected $uri;

    /**
     * Test to ensure the following requirement(s)
     *
     * 1) The path cannot contain any query string params (?)
     * 2) The path cannot contain any fragments (#)
     * 3) Attempting to assign an invalid PHP data-type throws an InvalidArguementException
     *
     */
    public function testWithPath()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Invalid path provided; must not contain a query string');
        $this->uri->withPath('?foo=foo');
        $this->uri->withPath('?');

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Invalid path provided; must not contain a URI fragment');
        $this->uri->withPath('#fragment');
        $this->uri->withPath('#');

        self::expectException(InvalidArgumentException::class);
        $this->uri->withPath(true);
        $this->uri->withPath(25.5);
        $this->uri->withPath(new stdClass);
        $this->uri->withPath(null);
        $this->uri->withPath(['foo' => 'bar']);
        $this->uri->withPath(['foo']);
        $this->uri->withPath('');
    }

    /**
     *
     */
    public function testWithQuery()
    {

    }

    /**
     *
     */
    public function testWithFragment()
    {

    }

    public function testWithSchemaReturnsNewInstanceWhenNewScheme()
    {
        $uri = $this->uri->withScheme('http');
        self::assertNotSame($this->uri, $uri);
    }

    public function testWithSchemeReturnsSameInstanceWhenSameScheme()
    {
        $uri = $this->uri->withScheme('http');
        self::assertSame($uri, $uri->withScheme('http'));
    }

    public function testWithSchemeReturnsSameInstanceWhenEmptyScheme()
    {
        self::assertSame($this->uri, $this->uri->withScheme(''));
    }

    public function testWithSchemeConvertsToLowerCase()
    {
        self::assertEquals('http', $this->uri->withScheme('HTTP')->getScheme());
    }

    public function testWithSchemeRemovesExtraCharacters()
    {
        self::assertEquals('http', $this->uri->withScheme('HTTP:')->getScheme());
        self::assertEquals('http', $this->uri->withScheme('HTTP://')->getScheme());
    }

    public function invalidSchemeDataProvider()
    {
        return [
            ['ftp', 'Unsupported scheme requested "ftp"; must be empty or in the set (http, https)'],
            ['FTP', 'Unsupported scheme requested "ftp"; must be empty or in the set (http, https)'],
            ['ftp:', 'Unsupported scheme requested "ftp"; must be empty or in the set (http, https)'],
            ['FTP:', 'Unsupported scheme requested "ftp"; must be empty or in the set (http, https)'],
            ['ftp://', 'Unsupported scheme requested "ftp"; must be empty or in the set (http, https)'],
            ['FTP://', 'Unsupported scheme requested "ftp"; must be empty or in the set (http, https)'],
        ];
    }

    /**
     * @param $scheme
     * @param $message
     * @dataProvider invalidSchemeDataProvider
     */
    public function testWithSchemeOnlyAcceptsHttpAndHttps($scheme, $message)
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage($message);
        $this->uri->withScheme($scheme);
    }

    public function invalidDataTypeDataProvider()
    {
        return [
            [1234],
            [true],
            [10.5],
            [[]],
            [['foo']],
            [['foo' => 'foo']],
            [new stdClass],
        ];
    }

    /**
     *
     * @param $scheme
     * @dataProvider invalidDataTypeDataProvider
     */
    public function testWithSchemeThrowsInvalidArgumentExceptionIfInvalidType($scheme)
    {
        $message = sprintf(
            '%s expects a string argument; received %s',
            Uri::class . '::withScheme',
            (is_object($scheme)) ? get_class($scheme) : gettype($scheme)
        );

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage($message);
        $this->uri->withScheme($scheme);
    }

    public function testWithUserInfoAcceptsUserPart()
    {
        self::assertEquals('foo', $this->uri->withUserInfo('foo')->getUserInfo());
    }

    public function testWithUserInfoRejectsPasswordIfEmpty()
    {
        self::assertEquals('foo', $this->uri->withUserInfo('foo', '')->getUserInfo());
    }

    public function testWithUserInfoAcceptsPassword()
    {
        self::assertEquals('foo:bar', $this->uri->withUserInfo('foo', 'bar')->getUserInfo());
    }

    public function testWithUserInfoIgnoresPasswordIfUserIsEmpty()
    {
        self::assertEquals('', $this->uri->withUserInfo('', 'bar')->getUserInfo());
    }

    public function credentialsDataProvider()
    {
        return [
            ['', ''],
            ['foo', ''],
            ['foo', 'bar'],
        ];
    }

    /**
     * @param $user
     * @param $password
     * @dataProvider credentialsDataProvider
     */
    public function testWithUserInfoReturnsSameInstanceIfPassedDetailsAreTheSame($user, $password)
    {
        $uri = $this->uri->withUserInfo($user, $password);
        self::assertSame($uri, $uri->withUserInfo($user, $password));
    }

    /**
     * @param $user
     * @param $password
     * @dataProvider credentialsDataProvider
     */
    public function testWithUserInfoReturnsNewInstanceIfPasseDetailsAreDifferent($user, $password)
    {
        $uri = $this->uri->withUserInfo('jason', 'lamb');
        self::assertNotSame($uri, $this->uri->withUserInfo($user, $password));
    }

    /**
     *
     * @param $userInfo
     * @dataProvider invalidDataTypeDataProvider
     */
    public function testWithUserInfoInvalidArgumentExceptionIfInvalidTypeForUser($userInfo)
    {
        $message = sprintf(
            '%s expects a string argument; received %s',
            Uri::class . '::withUserInfo',
            (is_object($userInfo)) ? get_class($userInfo) : gettype($userInfo)
        );

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage($message);
        $this->uri->withUserInfo($userInfo);
    }

    /**
     *
     * @param $userInfo
     * @dataProvider invalidDataTypeDataProvider
     */
    public function testWithUserInfoInvalidArgumentExceptionIfInvalidTypeForPassword($userInfo)
    {
        $message = sprintf(
            '%s expects a string argument; received %s',
            Uri::class . '::withUserInfo',
            (is_object($userInfo)) ? get_class($userInfo) : gettype($userInfo)
        );

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage($message);
        $this->uri->withUserInfo('foo', $userInfo);
    }

    public function withUserInfoEncodingDataProvider()
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
     * @dataProvider    withUserInfoEncodingDataProvider()
     */
    public function testWithUserInfoEncoding($user, $password, $expected)
    {
        self::assertEquals($expected, $this->uri->withUserInfo($user, $password)->getUserInfo());
    }

    public function testWithHostResetsHostIfHostIsEmpty()
    {
        self::assertEquals('', $this->uri->withHost('')->getHost());
    }

    public function hostDataProvider()
    {
        return [
            ['', ''],
            ['foo', ''],
            ['foo', 'bar'],
        ];
    }

    /**
     * @param $host
     * @dataProvider hostDataProvider
     */
    public function testWithHostReturnsSameInstanceIfPassedDetailsAreTheSame($host)
    {
        $uri = $this->uri->withHost($host);
        self::assertSame($uri, $uri->withHost($host));
    }

    /**
     * @param $host
     * @dataProvider hostDataProvider
     */
    public function testWithHostInfoReturnsNewInstanceIfPasseDetailsAreDifferent($host)
    {
        $uri = $this->uri->withHost('foobar');
        self::assertNotSame($uri, $this->uri->withHost($host));
    }

    /**
     * @param $host
     * @dataProvider invalidDataTypeDataProvider
     */
    public function testWithHostInfoInvalidArgumentExceptionIfInvalidTypeForUser($host)
    {
        $message = sprintf(
            '%s expects a string argument; received %s',
            Uri::class . '::withHost',
            (is_object($host)) ? get_class($host) : gettype($host)
        );

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage($message);
        $this->uri->withHost($host);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->uri = new Uri();
    }
}
