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
    }
}
