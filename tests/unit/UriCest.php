<?php
namespace Tests\Unit;

use BlcksheepIO\Http\Message\Uri;
use Codeception\Example;
use Faker\Factory;
use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use stdClass;
use Tests\UnitTester;

/**
 * Class UriCest
 *
 * Unit tests created in order to test
 * PSR-7 compliance.
 *
 * @package Tests\Unit
 */
class UriCest
{
    /**
     * @var Factory
     */
    protected $faker;

    /**
     * The UriInterface instance
     * currently under test.
     *
     * @var UriInterface $uri
     */
    protected $uri;

    /**
     * @param UnitTester $I
     */
    public function _before(UnitTester $I)
    {
        $this->faker = Factory::create();
        $this->uri = new Uri();
    }

    /**
     * Test to ensure that the scheme
     * provided is searched insensitively,
     * as per the PSR-7 requirements.
     *
     * @param UnitTester $I
     * @param Example $example
     * @dataprovider mixedCaseSchemeDataProvider
     * @group psr
     * @group psr_7
     * @group uri
     */
    public function tryToTestUriSchemeSearchesCaseInsensitive(UnitTester $I, Example $example)
    {
        $uri = $this->uri->withScheme($example['data']);
        $I->assertEquals('http', $uri->getScheme());
        $uri = $this->uri->withScheme($example['data'] . 's');
        $I->assertEquals('https', $uri->getScheme());
    }

    /**
     * Test to ensure that ONLY valid string
     * types will be accepted as a scheme.
     *
     * ALL other types will throw an exception.
     *
     * @param UnitTester $I
     * @param Example $example
     * @dataprovider invalidPHPDataTypeDataProvider
     * @group psr
     * @group psr_7
     * @group uri
     */
    public function tryToTestUriWithSchemeThrowsExceptionIfTypeStringIsNotPassed(UnitTester $I, Example $example)
    {
        $method = Uri::class . '::withScheme';
        $message = $method . ' expects a string argument; received ' . $example['type'];
        $I->expectException(
            new InvalidArgumentException($message),
            function () use ($example) {
                $this->uri->withScheme($example['data']);
            }
        );
    }

    /**
     * Test to ensure that Uri only accepts defined protocols.
     * By default the Uri will only accept the protocols
     * defined in the $acceptedProtocols list.
     *
     * @param UnitTester $I
     * @param Example $example
     * @dataprovider invalidSchemeDataProvider
     * @group psr
     * @group psr_7
     * @group uri
     */
    public function tryToTestUriWithSchemeThrowsExceptionIfInvalidSchemeIsPassed(UnitTester $I, Example $example)
    {
        $message = 'Unsupported scheme requested "ftp"; must be empty or in the set (http, https)';
        $I->expectException(
            new \InvalidArgumentException($message),
            function () use ($example) {
                $this->uri->withScheme($example['data']);
            }
        );
    }

    /**
     * @param UnitTester $I
     * @param Example $example
     * @dataprovider invalidCharsDataProvider
     * @group psr
     * @group psr_7
     * @group uri
     */
    public function tryToTestUriWithSchemeStripsOutUnncessaryCharacters(UnitTester $I, Example $example)
    {
        $I->assertEquals($example['result'], $this->uri->withScheme($example['data'])->getScheme());
    }

    /**
     * Test to ensure that if passing an empty scheme,
     * the UriInterface simply returns back an empty
     * scheme.
     *
     * This helps to test the immutability of the Uri
     * instance as defined in the PSR-7 documentation.
     *
     * @param UnitTester $I
     * @group psr
     * @group psr_7
     * @group uri
     */
    public function tryTestUriWithSchemeWithEmptySchemeParameterReturnsOriginalUriInstance(UnitTester $I)
    {
        $uri = $this->uri->withScheme('');
        $I->assertSame($uri, $this->uri);
        $uri = $uri->withScheme('https');
        $I->assertSame($uri, $uri->withScheme('https'));
    }

    /**
     * Test to ensure that if the passed scheme is different to the currently
     * assigned scheme, the UriInterface will correctly return a cloned instance
     * instead, thereby ensuring the immutability of the original instance.
     *
     * @param UnitTester $I
     * @group psr
     * @group psr_7
     * @group uri
     */
    public function tryTestUriWithSchemeWithDifferentSchemeParameterReturnsNewUriInstance(UnitTester $I)
    {
        $uri = $this->uri->withScheme('http');
        $I->assertNotSame($uri, $this->uri);
        $uri = $uri->withScheme('https');
        $I->assertNotSame($uri, $this->uri);
        $I->assertInstanceOf(Uri::class, $uri);
        $I->assertInstanceOf(UriInterface::class, $uri);
    }

    /**
     * Test to ensure that the withHost method
     * correctly returns the original Uri if the
     * host parameter is identical to the
     * exisiting host parameter.
     *
     * @param UnitTester $I
     * @group psr
     * @group psr_7
     * @group uri
     */
    public function tryToTestUriWithHostAndSameHostParameterReturnsOriginalUriInstance(UnitTester $I)
    {
        $uri = $this->uri->withHost('');
        $I->assertSame($uri, $this->uri);
        $uri = $uri->withHost('google.com');
        $I->assertSame($uri, $uri->withHost('google.com'));
    }

    /**
     * Test to ensure that the withHost function
     * correctly honors the immutability requirement
     * when passing in a different host name.
     *
     * @param UnitTester $I
     * @group psr
     * @group psr_7
     * @group uri
     */
    public function tryToTestUriWithHostAndDifferentHostParameterReturnNewUriInstance(UnitTester $I)
    {
        $uri = $this->uri->withHost('google.com');
        $I->assertNotSame($uri, $this->uri);
        $uri = $uri->withHost('php.net');
        $I->assertNotSame($uri, $this->uri);
        $I->assertInstanceOf(Uri::class, $uri);
        $I->assertInstanceOf(UriInterface::class, $uri);
    }

    /**
     * Test to ensure that userInfo $user portion
     * accepts only type string.
     *
     * @param UnitTester $I
     * @param Example $example
     * @dataprovider invalidPHPDataTypeDataProvider
     * @group psr
     * @group psr_7
     * @group uri
     */
    public function tryToTestUriWithUserInfoThrowsExceptionIfTypeStringIsNotPassedForUser(
        UnitTester $I,
        Example $example
    ) {
        $method = Uri::class . '::withUserInfo';
        $message = $method . ' expects a string argument; received ' . $example['type'];
        $I->expectException(
            new InvalidArgumentException($message),
            function () use ($example) {
                $this->uri->withUserInfo($example['data']);
            }
        );
    }

    /**
     * Test to ensure that userInfo $user portion
     * accepts only type string.
     *
     * @param UnitTester $I
     * @param Example $example
     * @dataprovider invalidPHPDataTypeDataProvider
     * @group psr
     * @group psr_7
     * @group uri
     */
    public function tryToTestUriWithUserInfoThrowsExceptionIfTypeStringIsNotPassedForPassword(
        UnitTester $I,
        Example $example
    ) {
        $method = Uri::class . '::withUserInfo';
        $message = $method . ' expects a string argument; received ' . $example['type'];
        $I->expectException(
            new InvalidArgumentException($message),
            function () use ($example) {
                $this->uri->withUserInfo($example['data'], $example['data']);
            }
        );
    }

    /**
     * Test to ensure that withUserInfo part does not accidentally
     * add the password.
     *
     * @param UnitTester $I
     * @group psr
     * @group psr_7
     * @group uri
     */
    public function tryToTestUriWithUserInfoDoesNotAddPasswordIfNotPresent(UnitTester $I)
    {
        $userInfoPart = $this->uri->withUserInfo('jlamb')->getUserInfo();
        $I->assertEquals('jlamb', $userInfoPart);
    }

    /**
     * Test to ensure that withUserInfo part does
     * add the password if it is present and valid.
     *
     * @param UnitTester $I
     * @group psr
     * @group psr_7
     * @group uri
     */
    public function tryToTestUriWithUserInfoDoesAddPasswordIfPresent(UnitTester $I)
    {
        $userInfoPart = $this->uri->withUserInfo('jlamb', 'password')->getUserInfo();
        $I->assertEquals('jlamb:password', $userInfoPart);
    }

    /**
     * Test to ensure that withUserInfo part honors the immutability
     * of the Uri by returning the same instance if the userInfo parts
     * are identical.
     *
     * @param UnitTester $I
     * @group psr
     * @group psr_7
     * @group uri
     */
    public function tryToTestUriWithUserInfoReturnsSameInstanceIfPassedSameUserInfo(UnitTester $I)
    {
        $uri = $this->uri->withUserInfo('');
        $I->assertSame($uri, $this->uri);
        $I->assertInstanceOf(Uri::class, $uri);
        $I->assertInstanceOf(UriInterface::class, $uri);
        $uri = $this->uri->withUserInfo('jason:lamb');
        $I->assertSame($uri, $uri->withUserInfo('jason:lamb'));
        $I->assertInstanceOf(Uri::class, $uri);
        $I->assertInstanceOf(UriInterface::class, $uri);
    }

    /**
     * Test to ensure that withUserInfo part honors the immutability
     * of the Uri by returning the same instance if the userInfo parts
     * are not identical.
     *
     * @param UnitTester $I
     * @group psr
     * @group psr_7
     * @group uri
     */
    public function tryToTestUriWithUserInfoReturnsNewInstanceIfPassedDifferentUserInfo(UnitTester $I)
    {
        $uri = $this->uri->withUserInfo('jason', 'lamb');
        $I->assertNotSame($uri, $this->uri);
        $I->assertInstanceOf(Uri::class, $uri);
        $I->assertInstanceOf(UriInterface::class, $uri);
    }

    /**
     * DataProvider used to test the UriInterface
     * with scheme will honor the requirement that
     * the scheme passed will validate regardless
     * of the requested case.
     *
     * @return array
     */
    protected function mixedCaseSchemeDataProvider()
    {
        return [
            ['data' => 'http'],
            ['data' => 'Http'],
            ['data' => 'hTtp'],
            ['data' => 'htTp'],
            ['data' => 'httP'],
            ['data' => 'HTtp'],
            ['data' => 'hTTp'],
            ['data' => 'htTP'],
            ['data' => 'HTTp'],
            ['data' => 'hTTP'],
            ['data' => 'HTTP'],
        ];
    }

    /**
     * DataProvider used to ensure that functions
     * will correctly throw an exception if an invalid
     * PHP data-type is passed.
     *
     * @return array
     */
    protected function invalidPHPDataTypeDataProvider()
    {
        return [
            ['data' => 1234, 'type' => 'integer'],
            ['data' => true, 'type' => 'boolean'],
            ['data' => new stdClass(), 'type' => stdClass::class],
            ['data' => null, 'type' => 'NULL'],
            ['data' => 10.5, 'type' => 'double'],
        ];
    }

    /**
     * DataProvider function used to ensure
     * that the withScheme function ONLY accepts
     * schemes defined.
     *
     * @return array
     */
    protected function invalidSchemeDataProvider()
    {
        return [
            ['data' => 'ftp'],
            ['data' => 'ftp://'],
            ['data' => 'ftp://'],
            ['data' => 'FTP'],
            ['data' => 'FTP://'],
        ];
    }

    /**
     * DataProvider used to ensure that any
     * redundant characters are correctly
     * stripped when using withScheme.
     *
     * @return array
     */
    protected function invalidCharsDataProvider()
    {
        return [
            ['data' => 'http:', 'result' => 'http'],
            ['data' => 'http://', 'result' => 'http'],
        ];
    }
}
