<?php

namespace Tests\Http\Message;

use BlcksheepIO\Http\Message\Uri;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Class UriTest
 * @package                       Tests\Http\Message
 * @author                        Jason Lamb <baaa@iamalamb.com>
 *
 * @TODO                          : FINISH THE TESTS!!!!!!!!!!!!!
 */
class UriTest extends TestCase
{

    /*
     * CONSTRUCTOR TESTS
     *
     * @TODO: - REMOVE ONCE TESTS ARE COMPLETED!
     */

    /**
     * Test to ensure that the contructor function correctly
     * sets the scheme.
     */
    public function testConstructorCorrectlySetsScheme()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        self::assertSame('https', $uri->getScheme());
    }

    /**
     * Test to ensure that the contructor function correctly
     * sets the user info.
     */
    public function testConstructorCorrectlySetsUserInfo()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        self::assertSame('user:pass', $uri->getUserInfo());
    }

    /**
     * Test to ensure that the contructor function correctly
     * sets the host.
     */
    public function testConstructorCorrectlySetsHost()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        self::assertSame('local.example.com', $uri->getHost());
    }

    /**
     * Test to ensure that the contructor function correctly
     * sets the port.
     */
    public function testConstructorCorrectlySetsPort()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        self::assertSame(3001, $uri->getPort());
    }

    /**
     * Test to ensure that the contructor function correctly
     * sets the path.
     */
    public function testConstructorCorrectlySetsPath()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        self::assertSame('/foo', $uri->getPath());
    }

    /**
     * Test to ensure that the contructor function correctly
     * sets the query.
     */
    public function testConstructorCorrectlySetsQuery()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        self::assertSame('bar=baz', $uri->getQuery());
    }

    /*
     * SCHEME TESTS
     *
     * @TODO: - REMOVE ONCE TESTS ARE COMPLETED!
     */

    /**
     * Ensures the immutability of a Uri instance. Test will
     * ensure that when passing in a new scheme, the Uri instance
     * is a new clone with the modified scheme.
     *
     * @return \Psr\Http\Message\UriInterface $uri
     */
    public function testWithSchemeReturnsNewUriInstanceWhenNewSchemeIsProvided()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withScheme('http');
        self::assertNotSame($uri, $new);

        return $new;

    }

    /**
     * Test to ensure that when an instance of uri is cloned,
     * the getScheme function correctly returns the scheme for the
     * new cloned instance.
     *
     * @depends testWithSchemeReturnsNewUriInstanceWhenNewSchemeIsProvided
     */
    public function testGetSchemeReturnsCorrectSchemeFromToString($new)
    {
        self::assertEquals('http://user:pass@local.example.com/foo?bar=baz#quz', (string)$new);
    }

    /**
     * Test to ensure that when an instance of uri is cloned,
     * the getScheme function correctly returns the scheme for the
     * new cloned instance.
     *
     * @depends testWithSchemeReturnsNewUriInstanceWhenNewSchemeIsProvided
     */
    public function testGetSchemeReturnsCorrectSchemeForClonedInstance($new)
    {
        self::assertEquals('http', $new->getScheme());
    }

    /**
     * Ensures the immutability of a Uri instance. Test will
     * ensure that when passing in an identical scheme, the Uri instance
     * is the original instance.
     *
     * @return \Psr\Http\Message\UriInterface $uri
     */
    public function testWithSchemeReturnsSameInstanceIfWhenSchemeIsIdentical()
    {
        $uri = new Uri('http://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withScheme('http');
        self::assertSame($uri, $new);

        return $new;
    }

    /**
     * Ensures that if the scheme passed is identical that
     * getScheme will return the same scheme.
     *
     * @param $new
     * @depends testWithSchemeReturnsSameInstanceIfWhenSchemeIsIdentical
     */
    public function testWithSchemeReturnsCorrectSchemeIfIdenticalSchemeIsUsed($new)
    {
        self::assertSame('http', $new->getScheme());
    }

    /**
     * Ensures that withScheme can accept an empty string and returns an empty string.
     *
     * @return \Psr\Http\Message\UriInterface $uri
     */
    public function testWithSchemeAcceptsEmptyStringAndReturnsAnEmptyString()
    {
        $uri = new Uri();
        $uri->withScheme('');
        self::assertEmpty($uri->getScheme());
    }

    /**
     * DataProvider to ensure that the Uri can convert
     * http(s) to lowercase and that neither scheme
     * will throw an exception if used.
     *
     * @return array
     */
    public function validSchemeDataProvider()
    {
        return [
            ['lower_http' => 'http', 'expected' => 'http'],
            ['mixed_http' => 'Http', 'expected' => 'http'],
            ['upper_http' => 'HTTP', 'expected' => 'http'],
            ['lower_https' => 'https', 'expected' => 'https'],
            ['mixed_https' => 'Https', 'expected' => 'https'],
            ['upper_https' => 'HTTPS', 'expected' => 'https'],
            ['postfix' => 'http://', 'expected' => 'http'],
        ];
    }

    /**
     * Ensures the scheme is correctly transformed to lowercase.
     *
     * @param $scheme
     * @param $expected
     * @dataProvider  validSchemeDataProvider
     */
    public function testWithSchemeConvertsSchemeToLowerCase($scheme, $expected)
    {
        $uri = new Uri();
        $new = $uri->withScheme($scheme);
        self::assertSame($expected, $new->getScheme());
    }

    /**
     * Ensures that scheme supports http(s) and that an InvalidArgument
     * is not thrown if either is used.
     *
     * @param $scheme
     * @param $expected
     * @dataProvider  validSchemeDataProvider
     */
    public function testWithSchemeSupportsHttpAndHttpsAndDoesNotRaiseException($scheme, $expected)
    {
        $uri = new Uri();
        $new = $uri->withScheme($scheme);
        self::assertEquals($expected, $new->getScheme());
    }

    /**
     * Tests that the "://" postfix is correctly removed
     * from the scheme.
     */
    public function testWithSchemeRemovesExtraCharacters()
    {
        $uri = new Uri();
        $new = $uri->withScheme('http://');
        self::assertEquals('http', $new->getScheme());
    }

    /**
     * Test to ensure that an empty string is returned if it
     * is not set.
     *
     * @return $uri;
     */
    public function testGetSchemeReturnsAnEmptyStringIfSchemeIsNotPresent()
    {
        $uri = new Uri();
        self::assertEmpty($uri->getScheme());

        return $uri;
    }

    /**
     * Ensure that getScheme returns an instance of string.
     *
     * @param $new
     * @depends testGetSchemeReturnsAnEmptyStringIfSchemeIsNotPresent
     */
    public function testThatGetSchemeReturnsAString($new)
    {
        self::assertInternalType('string', $new->getScheme());
    }

    /**
     * Ensures that only schemes defined are considered valid. By default
     * instances will only accept http(s). For all other schemes an
     * exception will be thrown.
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unsupported scheme requested
     */
    public function testWithSchemeThrowsExceptionIfInvalidSchemeIsRequested()
    {
        $uri = new Uri();
        $uri->withScheme('ftp');
    }

    /**
     * DataProvider used to provide PHP data-types
     * that will be considered invalid when passed
     * as an argument.
     *
     * @return array
     */
    public function invalidPHPDataTypesDataProvider()
    {
        return [
            ['integer' => 123],
            ['boolean' => true],
            ['object' => new stdClass()],
            ['float' => 10.59],
            ['array' => ['foo', 'bar']],
        ];
    }

    /**
     * Ensures that an exception is thrown when
     * an invalid PHP data-type is used as the scheme.
     *
     * @param $scheme
     * @dataProvider             invalidPHPDataTypesDataProvider
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage BlcksheepIO\Http\Message\Uri::withScheme expects a string argument;
     */
    public function testWithSchemeThrowsExceptionWhenInvalidDataTypeIsUsed($scheme)
    {
        $uri = new Uri();
        $uri->withScheme($scheme);
    }

    /*
     * USER INFO TESTS
     *
     * @TODO: - REMOVE ONCE TESTS ARE COMPLETED!
     */

    /**
     * DataProvider used to ensure that
     * that valid user data is provided.
     *
     * @return array
     */
    public function validUserDataProvider()
    {
        return [
            ['user' => 'foo', 'password' => 'bar'],
            ['user' => 'foo', 'password' => ''],
            ['user' => 'foo', 'password' => null],
        ];
    }

    /**
     * Ensures immutability. Will test to ensure that if
     * new user info is provided, the withUserInfo will
     * return a cloned copy of the user instance.
     *
     * @TODO         : Check if this can't be broken into smaller tests?
     *
     * @param $user
     * @param $password
     * @dataProvider validUserDataProvider
     */
    public function testWithUserInfoReturnsNewInstanceWhenNewUserInfo($user, $password)
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withUserInfo($user, $password);
        self::assertNotSame($uri, $new);
    }

    /**
     * Immutability test to ensure that if the user info is identical, the
     * same user instance is returned and not cloned.
     */
    public function testWithUserInfoReturnsSameInstanceWhenUserAndPasswordAreIdentical()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withUserInfo('user', 'pass');
        self::assertSame($uri, $new);

        return $new;
    }

    /**
     * Ensures that when identical information is passed and the same instance
     * is returned, the user info is not altered.
     *
     * @param $new
     * @depends testWithUserInfoReturnsSameInstanceWhenUserAndPasswordAreIdentical
     */
    public function testGetUserInfoReturnsSameUserInfoWhenPassedIdenticalData($new)
    {
        self::assertSame('user:pass', $new->getUserInfo());
    }

    /**
     * Ensures that if an invalid PHP data-type is passed for the $user portion,
     * the withUser method will throw an exception.
     *
     * @param $user
     * @dataProvider             invalidPHPDataTypesDataProvider
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage BlcksheepIO\Http\Message\Uri::withUserInfo expects a string argument;
     */
    public function testWithUserInfoThrowsExceptionWhenUserIsInvalidDataType($user)
    {
        $uri = new Uri();
        $uri->withUserInfo($user);
    }

    /**
     * Test to ensure that if an invalid PHP data-type passed as the $password
     * portion will throw an exception.
     *
     * @param $user
     * @dataProvider             invalidPHPDataTypesDataProvider
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage BlcksheepIO\Http\Message\Uri::withUserInfo expects a string argument;
     */
    public function testWithUserInfoThrowsExceptionWhenPasswordIsInvalidDataType($password)
    {
        $uri = new Uri();
        $uri->withUserInfo('foo', $password);
    }

    /**
     * DataProvider used to provide valid combinations
     * user/password information.
     *
     * @return array
     */
    public function userInfoProviderDataProvider()
    {
        return [
            'valid-chars' => ['foo', 'bar', 'foo:bar'],
            'colon'       => ['foo:bar', 'baz:bat', 'foo%3Abar:baz%3Abat'],
            'at'          => ['user@example.com', 'cred@foo', 'user%40example.com:cred%40foo'],
            'percent'     => ['%25', '%25', '%25:%25'],
            'invalid-enc' => ['%ZZ', '%GG', '%25ZZ:%25GG'],
        ];
    }

    /**
     * Tests to ensure that the user/password combination is
     * correctly encoded.
     *
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

    /*
     * HOST TESTS
     *
     * @TODO: - REMOVE ONCE TESTS ARE COMPLETED!
     */

    /**
     * Ensures immutability by testing that a new instance of the uri
     * is returned if different data is provided.
     *
     * @return \Psr\Http\Message\UriInterface $new
     */
    public function testWithHostReturnsNewInstanceWhenNewHostIsProvided()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withHost('foo.com');
        self::assertNotSame($uri, $new);

        return $new;
    }

    /**
     * Test to ensure that when a clone is returned with new host info,
     * the getHost method will correctly return the newly assigned
     * host information.
     *
     * @param $new
     * @depends testWithHostReturnsNewInstanceWhenNewHostIsProvided
     */
    public function testGetHostCorrectlyReturnsNewHostInfoIfNewInstanceWasReturned($new)
    {
        self::assertSame('foo.com', $new->getHost());
    }

    /**
     * Immutability test to ensure that if passed an
     * identical host, the same instance is returned.
     *
     * @return \Psr\Http\Message\UriInterface $new
     */
    public function testWithHostReturnsSameInstanceWhenHostIsIdentical()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withHost('local.example.com');
        self::assertSame($uri, $new);

        return $new;
    }

    /**
     * Test to ensure that the host information is not modified
     * when assigning identical host information.
     *
     * @param $new
     * @depends testWithHostReturnsSameInstanceWhenHostIsIdentical
     */
    public function testGetHostReturnIdenticalHostInformationWhenIdenticalHostInformationIsPassed($new)
    {
        self::assertSame('local.example.com', $new->getHost());
    }

    /**
     * Ensures that the host portion accepts an
     * empty string as a parameter.
     *
     * @return $new;
     */
    public function testWithHostAcceptsEmptyString()
    {
        $uri = new Uri();
        $new = $uri->withHost('');
        self::assertSame($uri, $new);

        return $new;
    }

    /**
     * Test to ensure that if an empty string is passed,
     * or the host is NOT set, no modification is made
     * and the host is returned as an empty string.
     *
     * @param $new
     * @depends testWithHostAcceptsEmptyString
     */
    public function testGetHostReturnsAnEmptyStringIfOneIsPassed($new)
    {
        self::assertEmpty($new->getHost());
    }

    /**
     * Tests that the host portion is correctly normalized to lowercase
     * as specified in the original requirements.
     */
    public function testWithHostConvertsHostToLowerCase()
    {
        $uri = new Uri();
        $new = $uri->withHost('FOO.COM');
        self::assertSame('foo.com', $new->getHost());
    }

    /**
     * Ensures that an exception is thrown when passed an invalid data-type.
     *
     * @param $host
     * @dataProvider             invalidPHPDataTypesDataProvider
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage BlcksheepIO\Http\Message\Uri::withHost expects a string argument;
     */
    public function testWithHostThrowsExceptionWhenInvalidDataTypeIsUsed($host)
    {
        $uri = new Uri();
        $uri->withHost($host);
    }

    /*
     * PORT TESTS
     *
     * @TODO: - REMOVE ONCE TESTS ARE COMPLETED!
     */

    /**
     * Immutability test to ensure that if a new port
     * is specificed, a cloned instance is returned
     *
     * @return $new
     */
    public function testWithPortReturnsNewInstanceWhenNewPortIsProvided()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withPort(80);
        self::assertNotSame($uri, $new);

        return $new;
    }

    /**
     * Test to ensure that when cloning, the port is
     * correctly assigned to the new instance.
     *
     * @param $new
     * @depends testWithPortReturnsNewInstanceWhenNewPortIsProvided
     */
    public function testWithPortCorrectlyAssignsThePortToTheClonedInstance($new)
    {
        self::assertSame(80, $new->getPort());
    }

    /**
     * Immutability test to ensure that if an identical port
     * is provided, no cloning occurs.
     *
     * @return $new
     */
    public function testWithPortReturnsSameInstanceIfWhenPortIsIdentical()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withPort(3001);
        self::assertSame($uri, $new);

        return $new;
    }

    /**
     * Test to ensure that if an identical port is passed and
     * no cloning occurs, then the same port is returned.
     *
     * @param $new
     * @depends testWithPortReturnsSameInstanceIfWhenPortIsIdentical
     */
    public function testWithPortReturnsIdenticalPortIfSamePortIsPassed($new)
    {
        self::assertEquals(3001, $new->getPort());
    }

    /**
     * DataProvider used to provide valid ports
     *
     * @return array
     */
    public function validPortDataTypesDataProvider()
    {
        return [
            'integer' => [80],
            'string'  => ['80'],
            'null'    => [null],
        ];
    }

    /**
     * Ensures that valid ports can be assigned.
     *
     * @param $port
     * @dataProvider validPortDataTypesDataProvider
     */
    public function testWithPortAcceptsValidDataTypes($port)
    {
        $uri = new Uri();
        $new = $uri->withPort($port);
        self::assertEquals($port, $new->getPort());
    }

    /**
     * DataProvider used to ensure that invalid
     * PHP data-types cannot be used as ports.
     *
     * @return array
     */
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
     * Ensures that an exception is thrown
     * when an invalid PHP data-type is
     * passed as the port.
     *
     * @param $port
     * @dataProvider             invalidPortDataTypesDataProvider
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid port
     */
    public function testWithPortThrowsExceptionWhenInvalidDataTypeIsUsed($port)
    {
        $uri = new Uri();
        $uri->withPort($port);
    }

    /**
     * Test to ensure that an integer is returned
     * if the port is valid.
     */
    public function testWithPortReturnsAnIntegerIfPortIsValid()
    {
        $uri = new Uri();
        self::assertInternalType('integer', $uri->withPort('80')->getPort());
    }

    /**
     * Ensures that if a port is not specified NULL is returned.
     */
    public function testWithPortReturnsNullIfPortIsNotPresent()
    {
        $uri = new Uri();
        self::assertNull($uri->getPort());
    }

    /*
     * PATH TESTS
     *
     * @TODO: - REMOVE ONCE TESTS ARE COMPLETED!
     */

    /**
     * Test to ensure immutability is honoured and if a
     * new path is provided, the instance is cloned and returned.
     *
     * @return $new
     */
    public function testWithPathReturnsNewInstanceWhenNewPathIsProvided()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withPath('/foo/bar');
        self::assertNotSame($uri, $new);

        return $new;
    }

    /**
     * Test to ensure that if cloned, the new instance
     * correctly keeps the new path info.
     *
     * @param $new
     * @depends testWithPathReturnsNewInstanceWhenNewPathIsProvided
     */
    public function testToEnsureClonedInstanceReturnsNewPath($new)
    {
        self::assertSame('/foo/bar', $new->getPath());
    }

    /**
     * Test to ensure that if the path is identical then
     * the same instance is returned and no clone is present.
     *
     * @return $new
     */
    public function testWithPathReturnsSameInstanceIfPathIsIdentical()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withPath('/foo');
        self::assertSame($uri, $new);

        return $new;
    }

    /**
     * Test to ensure that the path is not modified if
     * attempting to assign an identical path.
     *
     * @param $new
     * @depends testWithPathReturnsSameInstanceIfPathIsIdentical
     */
    public function testWithPathReturnsCorrectPathIfPathIsIdentical($new)
    {
        self::assertSame('/foo', $new->getPath());
    }

    /**
     * DataProvider used to provide
     * PHP data-types that are considered
     * invalid paths.
     *
     * @return array
     */
    public function invalidPathDataProvider()
    {
        return [
            'null'   => [null],
            'true'   => [true],
            'false'  => [false],
            'array'  => [['/bar/baz']],
            'object' => [(object)['/bar/baz']],
        ];
    }

    /**
     * Ensures that an exception is thrown when
     * and invalid PHP data-type as used in the
     * assignment of the path.
     *
     * @param $path
     * @dataProvider             invalidPathDataProvider
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid path provided; must be a string
     */
    public function testWithPathThrowsExceptionWhenInvalidDataTypeIsUsed($path)
    {
        $uri = new Uri();
        $uri->withPath($path);
    }

    /**
     * Test to ensure that the query string is not considered
     * part of the path and will thrown an exception if passed.
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid path provided; must not contain a query string
     */
    public function testWithPathThrowsExecptionWhenQueryStringIsProvided()
    {
        $uri = new Uri();
        $uri->withPath('/foo?bar=baz');
    }

    /**
     * Ensures that an exception is thrown if the fragement is
     * passed with the path argument.
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid path provided; must not contain a URI fragment
     */
    public function testWithPathThrowsExecptionWhenUrlFragmentIsProvided()
    {
        $uri = new Uri();
        $uri->withPath('/foo#bar');
    }

    /*
     * QUERY TESTS
     *
     * @TODO: - REMOVE ONCE TESTS ARE COMPLETED!
     */

    /**
     * Immutability test to ensure that the instance
     * is correctly cloned if attempting to pass
     * a new query.
     *
     * @return $new
     */
    public function testWithQueryReturnsNewInstanceWhenNewQuery()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withQuery('?foo=bar&bar=baz');
        self::assertNotSame($uri, $new);

        return $new;
    }

    /**
     * Tests that if cloned, the new query data is returned.
     *
     * @param $new
     * @depends testWithQueryReturnsNewInstanceWhenNewQuery
     */
    public function testGetQueryReturnsNewQueryIfUriIsCloned($new)
    {
        self::assertSame('foo=bar&bar=baz', $new->getQuery());
    }

    /**
     * Test to ensure that if an identical is passed, then
     * the same instance is returned and no cloning takes place.
     *
     * @return $new
     */
    public function testWithQueryReturnsSameInstanceIfQueryIsIdentical()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withQuery('?bar=baz');
        self::assertSame($uri, $new);

        return $new;
    }

    /**
     * Test to ensure that if an identical uri is returned
     * from setting an identical query portion, the query
     * is not modified.
     *
     * @param $new
     * @depends testWithQueryReturnsSameInstanceIfQueryIsIdentical
     */
    public function testGetQueryReturnsSameQueryIfQueryIsIdentical($new)
    {
        self::assertSame('bar=baz', $new->getQuery());
    }

    /**
     * DataProvider used to pass in data-types considered
     * invalid.
     *
     * @return array
     */
    public function invalidQueryStringsDataProvider()
    {
        return [
            'null'   => [null],
            'true'   => [true],
            'false'  => [false],
            'array'  => [['baz=bat']],
            'object' => [(object)['baz=bat']],
        ];
    }

    /**
     * Test to ensure that an exception is thrown if
     * attempting to pass in an invalid query.
     *
     * @param $query
     * @dataProvider invalidQueryStringsDataProvider
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage BlcksheepIO\Http\Message\Uri::withQuery expects a string argument;
     */
    public function testWithQueryThrowsExceptionIfInvalidQueryIsPassed($query)
    {
        $uri = new Uri();
        $uri->withQuery($query);
    }

    /**
     * Ensures an exception is thrown if attempting
     * to passing in a uri framgement.
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Query string must not include a URI fragment
     */
    public function testWithQueryThrowsExceptionIfUrlFragmentIsPassed()
    {
        $uri = new Uri();
        $uri->withQuery('?test=test#myfragment');
    }

    /*
    * FRAGMENT TESTS
    *
    * @TODO: - REMOVE ONCE TESTS ARE COMPLETED!
    */

    /**
     * Test to ensure that if an identical fragment is passed, then
     * the same instance is returned and no cloning takes place.
     *
     * @return $new
     */
    public function testWithFragmentReturnsSameInstanceIfFragmentIsIdentical()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#foo');
        $new = $uri->withFragment('foo');
        self::assertSame($uri, $new);

        return $new;
    }

    /**
     * Test to ensure that if an identical uri is returned
     * from setting an identical query portion, the query
     * is not modified.
     *
     * @param $new
     * @depends testWithFragmentReturnsSameInstanceIfFragmentIsIdentical
     */
    public function testGetFragmentReturnsSameFragmentIfFragmentIsIdentical($new)
    {
        self::assertSame('foo', $new->getFragment());
    }

    /**
     * Immutability test to ensure that the instance
     * is correctly cloned if attempting to pass
     * a new query.
     *
     * @return $new
     */
    public function testWithFragmentReturnsNewInstanceWhenNewFragment()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withFragment('foo');
        self::assertNotSame($uri, $new);

        return $new;
    }
}
