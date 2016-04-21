<?php
namespace Sizzle\Bacon\Tests\Text;

/**
 * This class tests the CamelToUnderscore trait
 *
 * phpunit --bootstrap src/tests/autoload.php src/Tests/Traits/CamelToUnderscoreTest
 */
class CamelToUnderscoreTest
extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the fromCamelCase function.
     */
    public function testFromCamelCase()
    {
        $Helper = new Helper();
        $this->assertTrue(method_exists($Helper, 'fromCamelCase'));
        $this->assertEquals($Helper->fromCamelCase('simpleTest'), 'simple_test');
        $this->assertEquals($Helper->fromCamelCase('easy'), 'easy');
        $this->assertEquals($Helper->fromCamelCase('HTML'), 'html');
        $this->assertEquals($Helper->fromCamelCase('simpleXML'), 'simple_xml');
        $this->assertEquals($Helper->fromCamelCase('PDFLoad'), 'pdf_load');
        $this->assertEquals($Helper->fromCamelCase('startMIDDLELast'), 'start_middle_last');
        $this->assertEquals($Helper->fromCamelCase('AString'), 'a_string');
        $this->assertEquals($Helper->fromCamelCase('Some4Numbers234'), 'some4_numbers234');
        $this->assertEquals($Helper->fromCamelCase('TEST123String'), 'test123_string');
    }
}
