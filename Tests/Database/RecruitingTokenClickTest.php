<?php
namespace Sizzle\Bacon\Tests\Database;

use \Sizzle\Bacon\Database\RecruitingTokenClick;

/**
 * This class tests the Support class
 *
 * vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Database/RecruitingTokenClickTest
 */
class RecruitingTokenClickTest extends \PHPUnit_Framework_TestCase
{
    use \Sizzle\Bacon\Tests\Traits\RecruitingToken;


    /**
     * Tests the __construct function.
     */
    public function testCreate()
    {
        $html_tag_id = 'dog-food-'.rand();
        $html_tag = '<div id=".$html_tag_id." class="awesome">';
        $visitor_cookie = substr(md5(microtime()), rand(0, 26), 20);
        $token = $this->createRecruitingToken();

        $rtc = (new RecruitingTokenClick())->create($html_tag_id, $html_tag, $visitor_cookie, $token->id);
        $this->assertEquals($rtc->html_tag_id, $html_tag_id);
        $this->assertEquals($rtc->html_tag, $html_tag);
        $this->assertEquals($rtc->visitor_cookie, $visitor_cookie);
        $this->assertEquals($rtc->recruiting_token_id, $token->id);
    }
}
