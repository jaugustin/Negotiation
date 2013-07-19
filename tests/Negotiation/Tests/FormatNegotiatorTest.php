<?php

namespace Negotiation\Tests;

use Negotiation\FormatNegotiator;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class FormatNegotiatorTest extends TestCase
{
    protected function setUp()
    {
        $this->negotiator = new FormatNegotiator();
    }

    /**
     * @dataProvider dataProviderForGetBest
     */
    public function testGetBest($acceptHeader, $priorities, $expected)
    {
        $acceptHeader = $this->negotiator->getBest($acceptHeader, $priorities);

        if (null === $expected) {
            $this->assertNull($acceptHeader);
        } else {
            $this->assertNotNull($acceptHeader);
            $this->assertEquals($expected, $acceptHeader->getValue());
        }
    }

    /**
     * @dataProvider dataProviderForGetBestFormat
     */
    public function testGetBestFormat($acceptHeader, $priorities, $expected)
    {
        $this->markTestSkipped('Not yet implemented');

        $bestFormat = $this->negotiator->getBestFormat($acceptHeader, $priorities);

        $this->assertEquals($expected, $bestFormat);
    }

    public static function dataProviderForGetBest()
    {
        $pearAcceptHeader = 'text/html,application/xhtml+xml,application/xml;q=0.9,text/*;q=0.7,*/*,image/gif; q=0.8, image/jpeg; q=0.6, image/*';

        return array(
            // PEAR HTTP2 tests
            array(
                $pearAcceptHeader,
                array(
                    'image/gif',
                    'image/png',
                    'application/xhtml+xml',
                    'application/xml',
                    'text/html',
                    'image/jpeg',
                    'text/plain',
                ),
                'text/html'
            ),
            array(
                $pearAcceptHeader,
                array(
                    'image/gif',
                    'image/png',
                    'application/xhtml+xml',
                    'application/xml',
                    'image/jpeg',
                    'text/plain',
                ),
                'application/xhtml+xml'
            ),
            array(
                $pearAcceptHeader,
                array(
                    'image/gif',
                    'image/png',
                    'application/xml',
                    'image/jpeg',
                    'text/plain',
                ),
                'application/xml'
            ),
            array(
                $pearAcceptHeader,
                array(
                    'image/gif',
                    'image/png',
                    'image/jpeg',
                    'text/plain',
                ),
                'image/gif'
            ),
            array(
                $pearAcceptHeader,
                array(
                    'image/png',
                    'image/jpeg',
                    'text/plain',
                ),
                'text/plain'
            ),
            array(
                $pearAcceptHeader,
                array(
                    'image/png',
                    'image/jpeg',
                ),
                'image/jpeg'
            ),
            array(
                $pearAcceptHeader,
                array(
                    'image/png',
                ),
                'image/png'
            ),
            array(
                $pearAcceptHeader,
                array(
                    'audio/midi',
                ),
                'audio/midi'
            ),
        );
    }

    public static function dataProviderForGetBestFormat()
    {
        return array(
            array(null, array('html', 'json', '*/*'), null),
            array('text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', array(), 'html'),
            array('text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', array('html', 'json', '*/*'), 'html'),
            array('text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', array('html', 'json', '*/*'), 'html'),
            array('text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', array('rss', '*/*'), 'html'),
            array('text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', array('xml'), 'xml'),
            array('text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', array('json', 'xml'), 'xml'),
            array('text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', array('json'), 'json'),
            array('text/html,application/xhtml+xml,application/xml;q=0.9,*/*', array('json'), 'json'),
            array('text/html,application/xhtml+xml,application/xml;q=0.9,*/*', array('json'), 'json'),
            array('text/html,application/xhtml+xml,application/xml', array('json'), null),
            array('text/plain; q=0.5, text/html, text/x-dvi; q=0.8, text/x-c', array('*/*'), 'html'),
            array('text/html, application/json;q=0.8, text/csv;q=0.7', array(), 'html'),
        );
    }

    public function testGetFormat()
    {
        $this->assertEquals('html', $this->negotiator->getFormat('application/xhtml+xml'));
    }

    public function testGetFormatReturnsNullIfNotFound()
    {
        $this->assertNull($this->negotiator->getFormat('foo'));
    }

    public function testRegisterFormat()
    {
        $format   = 'foo';
        $mimeType = 'foo/bar';

        $this->negotiator->registerFormat($format, array($mimeType));
        $this->assertEquals($format, $this->negotiator->getFormat($mimeType));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Format "html" already registered, and override was set to "false".
     */
    public function testRegisterFormatWithExistingFormat()
    {
        $this->negotiator->registerFormat('html', array());
    }
}
