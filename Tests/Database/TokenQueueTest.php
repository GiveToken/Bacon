<?php
namespace Sizzle\Bacon\Tests\Database;

use Sizzle\Bacon\{
    Connection,
    Database\TokenQueue
};

/**
 * This class tests the TokenQueue class
 *
 * ./vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Database/TokenQueueTest
 */
class TokenQueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the __construct function.
     */
    public function testConstructor()
    {
        // $id = null case
        $tokenQueue = new TokenQueue();
        $this->assertEquals('Sizzle\Bacon\Database\TokenQueue', get_class($tokenQueue));
        $this->assertFalse(isset($tokenQueue->id));

        // test constuctor with id
        $tokenQueue = new TokenQueue();
        $tokenQueue->email_address = rand().'@gosizzle.io';
        $tokenQueue->subject = rand();
        $tokenQueue->body = rand();
        $tokenQueue->source = 'WEBSITE_UPLOAD';
        $tokenQueue->save();
        $this->assertTrue(isset($tokenQueue->id));
        $tokenQueue2 = new TokenQueue($tokenQueue->id);
        $this->assertEquals($tokenQueue->id, $tokenQueue2->id);
        $this->assertEquals($tokenQueue->email_address, $tokenQueue2->email_address);
        $this->assertEquals($tokenQueue->subject, $tokenQueue2->subject);
        $this->assertEquals($tokenQueue->body, $tokenQueue2->body);
        $this->assertEquals($tokenQueue->source, $tokenQueue2->source);
    }

    /**
     * Tests the markWorked function.
     */
    public function testMarkWorked()
    {
        $tokenQueue = new TokenQueue();
        $tokenQueue->email_address = rand().'@gosizzle.io';
        $tokenQueue->subject = rand();
        $tokenQueue->body = rand();
        $tokenQueue->source = 'WEBSITE_UPLOAD';
        $tokenQueue->save();
        $this->assertFalse(isset($tokenQueue->worked));
        $this->assertTrue($tokenQueue->markWorked());

        $tokenQueue2 = new TokenQueue($tokenQueue->id);
        $this->assertTrue(isset($tokenQueue2->worked));
        $this->assertFalse($tokenQueue2->markWorked());
    }

    /**
     * Tests the getUnworked function.
     */
    public function testGetUnworked()
    {
        $this->markTestIncomplete();
    }
}
