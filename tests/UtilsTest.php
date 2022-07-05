<?php

namespace Guzzle6Http\Test;

use Guzzle6Http;
use Guzzle6Http\Utils;
use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{
    public function noBodyProvider()
    {
        return [['get'], ['head'], ['delete']];
    }

    public function typeProvider()
    {
        return [
            ['foo', 'string(3) "foo"'],
            [true, 'bool(true)'],
            [false, 'bool(false)'],
            [10, 'int(10)'],
            [1.0, 'float(1)'],
            [new StrClass(), 'object(Guzzle6Http\Test\StrClass)'],
            [['foo'], 'array(1)']
        ];
    }

    /**
     * @dataProvider typeProvider
     */
    public function testDescribesType($input, $output)
    {
        /**
         * Output may not match if Xdebug is loaded and overloading var_dump().
         *
         * @see https://xdebug.org/docs/display#overload_var_dump
         */
        if (extension_loaded('xdebug')) {
            $originalOverload = ini_get('xdebug.overload_var_dump');
            ini_set('xdebug.overload_var_dump', 0);
        }

        try {
            self::assertSame($output, Utils::describeType($input));
            self::assertSame($output, Guzzle6Http\describe_type($input));
        } finally {
            if (extension_loaded('xdebug')) {
                ini_set('xdebug.overload_var_dump', $originalOverload);
            }
        }
    }

    public function testParsesHeadersFromLines()
    {
        $lines = [
            'Foo: bar',
            'Foo: baz',
            'Abc: 123',
            'Def: a, b',
        ];

        $expected = [
            'Foo' => ['bar', 'baz'],
            'Abc' => ['123'],
            'Def' => ['a, b'],
        ];

        self::assertSame($expected, Utils::headersFromLines($lines));
        self::assertSame($expected, Guzzle6Http\headers_from_lines($lines));
    }

    public function testParsesHeadersFromLinesWithMultipleLines()
    {
        $lines = ['Foo: bar', 'Foo: baz', 'Foo: 123'];
        $expected = ['Foo' => ['bar', 'baz', '123']];

        self::assertSame($expected, Utils::headersFromLines($lines));
        self::assertSame($expected, Guzzle6Http\headers_from_lines($lines));
    }

    public function testChooseHandler()
    {
        self::assertIsCallable(Utils::chooseHandler());
        self::assertIsCallable(Guzzle6Http\choose_handler());
    }

    public function testDefaultUserAgent()
    {
        self::assertIsString(Utils::defaultUserAgent());
        self::assertIsString(Guzzle6Http\default_user_agent());
    }

    public function testReturnsDebugResource()
    {
        self::assertIsResource(Utils::debugResource());
        self::assertIsResource(Guzzle6Http\debug_resource());
    }

    public function testProvidesDefaultCaBundler()
    {
        self::assertFileExists(Utils::defaultCaBundle());
        self::assertFileExists(Guzzle6Http\default_ca_bundle());
    }

    public function testNormalizeHeaderKeys()
    {
        $input = ['HelLo' => 'foo', 'WORld' => 'bar'];
        $expected = ['hello' => 'HelLo', 'world' => 'WORld'];

        self::assertSame($expected, Utils::normalizeHeaderKeys($input));
        self::assertSame($expected, Guzzle6Http\normalize_header_keys($input));
    }

    public function noProxyProvider()
    {
        return [
            ['mit.edu', ['.mit.edu'], false],
            ['foo.mit.edu', ['.mit.edu'], true],
            ['foo.mit.edu:123', ['.mit.edu'], true],
            ['mit.edu', ['mit.edu'], true],
            ['mit.edu', ['baz', 'mit.edu'], true],
            ['mit.edu', ['', '', 'mit.edu'], true],
            ['mit.edu', ['baz', '*'], true],
        ];
    }

    /**
     * @dataProvider noproxyProvider
     */
    public function testChecksNoProxyList($host, $list, $result)
    {
        self::assertSame($result, Utils::isHostInNoProxy($host, $list));
        self::assertSame($result, \Guzzle6Http\is_host_in_noproxy($host, $list));
    }

    public function testEnsuresNoProxyCheckHostIsSet()
    {
        $this->expectException(\InvalidArgumentException::class);

        Utils::isHostInNoProxy('', []);
    }

    public function testEnsuresNoProxyCheckHostIsSetLegacy()
    {
        $this->expectException(\InvalidArgumentException::class);

        \Guzzle6Http\is_host_in_noproxy('', []);
    }

    public function testEncodesJson()
    {
        self::assertSame('true', Utils::jsonEncode(true));
        self::assertSame('true', \Guzzle6Http\json_encode(true));
    }

    public function testEncodesJsonAndThrowsOnError()
    {
        $this->expectException(\InvalidArgumentException::class);

        Utils::jsonEncode("\x99");
    }

    public function testEncodesJsonAndThrowsOnErrorLegacy()
    {
        $this->expectException(\InvalidArgumentException::class);

        \Guzzle6Http\json_encode("\x99");
    }

    public function testDecodesJson()
    {
        self::assertTrue(Utils::jsonDecode('true'));
        self::assertTrue(\Guzzle6Http\json_decode('true'));
    }

    public function testDecodesJsonAndThrowsOnError()
    {
        $this->expectException(\InvalidArgumentException::class);

        Utils::jsonDecode('{{]]');
    }

    public function testDecodesJsonAndThrowsOnErrorLegacy()
    {
        $this->expectException(\InvalidArgumentException::class);

        \Guzzle6Http\json_decode('{{]]');
    }
}

final class StrClass
{
    public function __toString()
    {
        return 'foo';
    }
}
