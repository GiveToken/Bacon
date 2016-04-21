<?php
namespace Sizzle\Tests\Text;

use Sizzle\Bacon\Text\HTML;

/**
 * This class tests the HTML class
 *
 * ./vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Text/HTMLTest
 */
class HTMLTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the __construct function.
     */
    public function testConstructor()
    {
        // instantiation check
        $result = new HTML();
        $this->assertEquals('Sizzle\Bacon\Text\HTML', get_class($result));
    }

    /**
     * Tests the to function.
     */
    public function testTo()
    {
        // check for static method
        $this->assertTrue(method_exists('Sizzle\Bacon\Text\HTML', 'to'));

        // empty string test
        $text = '';
        $expected = '';
        $this->assertEquals($expected, HTML::to($text));

        // carriage return & newline test
        $text = "This\rtext\nis\r\nborken.";
        $expected = '<p>This</p><p>text</p><p>is</p><p>borken.</p>';
        $this->assertEquals($expected, HTML::to($text));

        // simple bullets
        $bullet_list = "This is my list\n•Nachoes\n•number 2\n•pizza\nWasn't that great?";
        //echo "\n".$bullet_list."\n";
        $expected = "<p>This is my list</p>";
        $expected .= "<p><ul><li>Nachoes</li><li>number 2</li><li>pizza</li></ul></p>";
        $expected .= "<p>Wasn't that great?</p>";
        $this->assertEquals($expected, HTML::to($bullet_list));

        // multiple sections of bullets

        // test all the bullets!
        $bullets = ['◘','○','◙','‣','⁃','⁌','⁍','⦾','⦿'];
        $bullet_list = "This is my list\n• simple bullet";
        $expected_list = '';
        foreach ($bullets as $bullet) {
            $bullet_list .= "\n".$bullet." other bullet";
            $expected_list .= '<li> other bullet</li>';
        }
        $bullet_list .= "\nWasn't that great?";
        $expected = "<p>This is my list</p><p><ul><li> simple bullet</li>";
        $expected .= $expected_list;
        $expected .= "</ul></p><p>Wasn't that great?</p>";
        $this->assertEquals($expected, HTML::to($bullet_list));

        // Robbie's fail case - bullets start text
        $text = "•	No college experience needed\n";
        $text .= "•	Ability to kick a field goal over 45 yards with 75% accuracy\n";
        $text .= "•	Ability to make an extra point with 95% accuracy\n";
        $expected = '<p><ul>';
        $expected .= '<li>	No college experience needed</li>';
        $expected .= '<li>	Ability to kick a field goal over 45 yards with 75% accuracy</li>';
        $expected .= '<li>	Ability to make an extra point with 95% accuracy</li>';
        $expected .= '</ul></p>';
        $this->assertEquals($expected, HTML::to($text));

        // single bullet
        $text = "•	No college experience needed";
        $expected = '<p><ul>';
        $expected .= '<li>	No college experience needed</li>';
        $expected .= '</ul></p>';
        $this->assertEquals($expected, HTML::to($text));

        // multiple bullet sections
        $text = "•	No college experience needed\n";
        $text .= "This is non bullet section 1.\n";
        $text .= "•	Ability to kick a field goal over 45 yards with 75% accuracy\n";
        $text .= "This is non bullet section 2.\n";
        $text .= "• Ability to make an extra point with 95% accuracy\n";
        $text .= "This is non bullet section 3.\n";
        $text .= "•This is bullet section 4!";
        $expected = '<p><ul>';
        $expected .= '<li>	No college experience needed</li>';
        $expected .= '</ul></p><p>This is non bullet section 1.</p><p><ul>';
        $expected .= '<li>	Ability to kick a field goal over 45 yards with 75% accuracy</li>';
        $expected .= '</ul></p><p>This is non bullet section 2.</p><p><ul>';
        $expected .= '<li> Ability to make an extra point with 95% accuracy</li>';
        $expected .= '</ul></p><p>This is non bullet section 3.</p><p><ul>';
        $expected .= '<li>This is bullet section 4!</li>';
        $expected .= '</ul></p>';
        $this->assertEquals($expected, HTML::to($text));
    }

    /**
     * Tests the from function.
     */
    public function testFrom()
    {
        // check for static method
        $this->assertTrue(method_exists('Sizzle\Bacon\Text\HTML', 'from'));

        // empty string test
        $html = '';
        $expected = '';
        $this->assertEquals($expected, HTML::from($html));

        // carriage return & newline test
        $html = '<p>This</p><p>text</p><p>is</p><p>borken.</p>';
        $expected = "This\r\ntext\r\nis\r\nborken.";
        $this->assertEquals($expected, HTML::from($html));

        // bullets
        $html = "<p>This is my list</p>";
        $html .= "<p><ul><li>Nachoes</li><li>number 2</li><li>pizza</li></ul></p>";
        $html .= "<p>Wasn't that great?</p>";
        $expected = "This is my list\r\n•Nachoes\r\n•number 2\r\n•pizza\r\nWasn't that great?";
        $this->assertEquals($expected, HTML::from($html));

        // Robbie's fail case - bullets start text
        $text = "•	No college experience needed\r\n";
        $text .= "•	Ability to kick a field goal over 45 yards with 75% accuracy\r\n";
        $text .= "•	Ability to make an extra point with 95% accuracy\r\n";
        $html = '<p><ul>';
        $html .= '<li>	No college experience needed</li>';
        $html .= '<li>	Ability to kick a field goal over 45 yards with 75% accuracy</li>';
        $html .= '<li>	Ability to make an extra point with 95% accuracy</li>';
        $html .= '</ul></p><p></p>';
        $this->assertEquals($text, HTML::from($html));

        // single bullet
        $text = "•	No college experience needed";
        $html = '<p><ul>';
        $html .= '<li>	No college experience needed</li>';
        $html .= '</ul></p>';
        $this->assertEquals($text, HTML::from($html));

        // multiple bullet sections
        $text = "•	No college experience needed\r\n";
        $text .= "This is non bullet section 1.\r\n";
        $text .= "•	Ability to kick a field goal over 45 yards with 75% accuracy\r\n";
        $text .= "This is non bullet section 2.\r\n";
        $text .= "• Ability to make an extra point with 95% accuracy\r\n";
        $text .= "This is non bullet section 3.\r\n";
        $text .= "•This is bullet section 4!";
        $html = '<p><ul>';
        $html .= '<li>	No college experience needed</li>';
        $html .= '</ul></p><p>This is non bullet section 1.</p><p><ul>';
        $html .= '<li>	Ability to kick a field goal over 45 yards with 75% accuracy</li>';
        $html .= '</ul></p><p>This is non bullet section 2.</p><p><ul>';
        $html .= '<li> Ability to make an extra point with 95% accuracy</li>';
        $html .= '</ul></p><p>This is non bullet section 3.</p><p><ul>';
        $html .= '<li>This is bullet section 4!</li>';
        $html .= '</ul></p>';
        $this->assertEquals($text, HTML::from($html));
    }

    /**
     * Tests that the functions are inverses of eachother.
     * Sort of.
     * To certain equivalence classes of characters.
     */
    public function testConvolution()
    {
        // empty string test
        $text = '';
        $convolution1 = HTML::from(HTML::to($text));
        $this->assertEquals($text, $convolution1);
        $convolution2 = HTML::to(HTML::from($text));
        $this->assertEquals($text, $convolution2);

        // carriage return & newline test
        // (this inverse assumes the pair is always used)
        $text = "This\r\ntext\r\nis\r\nborken.";
        $convolution1 = HTML::from(HTML::to($text));
        $this->assertEquals($text, $convolution1);
        $html = '<p>This</p><p>text</p><p>is</p><p>borken.</p>';
        $convolution2 = HTML::to(HTML::from($html));
        $this->assertEquals($html, $convolution2);

        // carriage return & newline test
        // (this inverse assumes bullet is always '•')
        $text = "This is my list\r\n•Nachoes\r\n•number 2\r\n•pizza\r\nWasn't that great?";
        $convolution1 = HTML::from(HTML::to($text));
        $this->assertEquals($text, $convolution1);
        $html = "<p>This is my list</p>";
        $html .= "<p><ul><li>Nachoes</li><li>number 2</li><li>pizza</li></ul></p>";
        $html .= "<p>Wasn't that great?</p>";
        $convolution2 = HTML::to(HTML::from($html));
        $this->assertEquals($html, $convolution2);

        // Robbie's fail case - bullets start text
        $text = "•	No college experience needed\r\n";
        $text .= "•	Ability to kick a field goal over 45 yards with 75% accuracy\r\n";
        $text .= "•	Ability to make an extra point with 95% accuracy";
        $convolution1 = HTML::from(HTML::to($text));
        $this->assertEquals($text, $convolution1);
        $html = '<p><ul>';
        $html .= '<li>	No college experience needed</li>';
        $html .= '<li>	Ability to kick a field goal over 45 yards with 75% accuracy</li>';
        $html .= '<li>	Ability to make an extra point with 95% accuracy</li>';
        $html .= '</ul></p>';
        $convolution2 = HTML::to(HTML::from($html));
        $this->assertEquals($html, $convolution2);

        // single bullet
        $text = "•	No college experience needed";
        $convolution1 = HTML::from(HTML::to($text));
        $this->assertEquals($text, $convolution1);
        $html = '<p><ul>';
        $html .= '<li>	No college experience needed</li>';
        $html .= '</ul></p>';
        $convolution2 = HTML::to(HTML::from($html));
        $this->assertEquals($html, $convolution2);

        // multiple bullet sections
        $text = "•	No college experience needed\r\n";
        $text .= "This is non bullet section 1.\r\n";
        $text .= "•	Ability to kick a field goal over 45 yards with 75% accuracy\r\n";
        $text .= "This is non bullet section 2.\r\n";
        $text .= "• Ability to make an extra point with 95% accuracy\r\n";
        $text .= "This is non bullet section 3.\r\n";
        $text .= "•This is bullet section 4!";
        $convolution1 = HTML::from(HTML::to($text));
        $this->assertEquals($text, $convolution1);
        $html = '<p><ul>';
        $html .= '<li>	No college experience needed</li>';
        $html .= '</ul></p><p>This is non bullet section 1.</p><p><ul>';
        $html .= '<li>	Ability to kick a field goal over 45 yards with 75% accuracy</li>';
        $html .= '</ul></p><p>This is non bullet section 2.</p><p><ul>';
        $html .= '<li> Ability to make an extra point with 95% accuracy</li>';
        $html .= '</ul></p><p>This is non bullet section 3.</p><p><ul>';
        $html .= '<li>This is bullet section 4!</li>';
        $html .= '</ul></p>';
        $convolution2 = HTML::to(HTML::from($html));
        $this->assertEquals($html, $convolution2);
    }
}
