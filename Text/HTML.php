<?php
namespace Sizzle\Bacon\Text;

/**
 * This class is for parsing strings into and out of HTML
 */
class HTML
{
    private static $bullets = ['◘','○','◙','‣','⁃','⁌','⁍','⦾','⦿'];

    /**
     * Converts plain text to HTML
     *
     * @param string $text - plain text to format in HTML
     *
     * @return string - HTML
     */
    public static function to(string $text)
    {
        if ('' == $text) {
            $html = $text;
        } else {
            $html = '<p>';

            // simplify carriage returns to newlines
            $text = str_replace("\r\n", "\n", $text);
            $text = str_replace("\r", "\n", $text);

            // bulleted lists
            if (self::hasBullet($text)) {
                // regularize bullets
                $text = self::replaceBullets($text);

                // find & process bulleted sections
                $tempText = $text;
                $sections = array();
                while (strpos($tempText, "•") !== false) {
                    $pos = strpos($tempText, "•");
                    $sections[] = substr($tempText, 0, $pos);
                    $textWithBullets = substr($tempText, $pos);
                    //echo $textWithBullets;die;

                    // find first newline not followed by bullet
                    $pieces = explode("\n", $textWithBullets);
                    $nextText = array_shift($pieces);
                    //echo $nextText;die;
                    $bulletSection = '';
                    while (strpos($nextText, "•") === 0) {
                        $bulletSection .= "\n".$nextText;
                        $nextText = array_shift($pieces);
                    }
                    // bullets to html list tags
                    $sections[] = self::bulletListTo($bulletSection);
                    $tempText = $nextText."\n".implode("\n", $pieces);
                }
                //print_r($sections);
                $text = implode("\n", $sections)."\n".$tempText;
            }

            // spacing between paragraphs
            $text = str_replace("\n", '</p><p>', $text);

            $html .= $text;
            $html .= '</p>';

            // clean up empty paragraphs
            $html = str_replace('<p></p>', '', $html);
        }
        return $html;
    }

    /**
     * Converts well-formed (not broken) HTML to plain text
     *
     * @param string $html - HTML to format in plain text
     *
     * @return string - plain text
     */
    public static function from(string $html)
    {
        $text = '';
        if ('' != $html) {
            // bullets
            $html = str_replace('<ul><li>', "•", $html);
            $html = str_replace('</li><li>', "\r\n•", $html);
            $html = str_replace('</li></ul>', "", $html);

            // spacing between paragraphs
            $html = str_replace('</p><p>', "\r\n", $html);
            $html = str_replace('<p>', '', $html);
            $html = str_replace('</p>', '', $html);
            $text .= $html;
        }
        return $text;
    }

    /**
     * Checks if text contains a bullet (•◘○◙‣⁃⁌⁍⦾⦿)
     *
     * @param string $text - plain text to check for bullet
     *
     * @return boolean - HTML
     */
    private static function hasBullet(string $text)
    {
        $has = strpos($text, "\n•") !== false;
        $has = $has || strpos($text, "\n •") !== false;
        $has = $has || strpos($text, "•") === 0;
        $i = 0;
        while (!$has && $i < count(self::$bullets)) {
            $has = $has || strpos($text, "\n".self::$bullets[$i]) !== false;
            $has = $has || strpos($text, "\n ".self::$bullets[$i]) !== false;
            $i++;
        }
        return $has;
    }

    /**
     * Replaces all bullets in string (◘○◙‣⁃⁌⁍⦾⦿) with simple ones (•)
     *
     * @param string $text - plain text to replace bullets in
     *
     * @return string - text with bullets replaced
     */
    private static function replaceBullets(string $text)
    {
        foreach (self::$bullets as $bullet) {
            $text = str_replace($bullet, '•', $text);
        }
        return $text;
    }

    /**
     * Takes a single bulleted list with no other text and HTMLifies it
     *
     * @param string $text - plain text with bullets
     *
     * @return string - text with HTML list tags
     */
    private static function bulletListTo(string $text)
    {
        // replace first bullet
        if (0 === strpos($text, "•")) {
            $text = substr_replace($text, '<ul><li>', 0, strlen("•"));
        } else {
            $pos = strpos($text, "\n•");
            $text = substr_replace($text, '</p><p><ul><li>', $pos, strlen("\n•"));
        }

        // replace middle bullets
        $text = str_replace("\n•", '</li><li>', $text);

        //end the list
        $pos = strrpos($text, '<li>');
        $backend = substr($text, $pos);
        if (strpos($backend, "\n") !== false) {
            $pos = strpos($backend, "\n");
            $pos = $pos + strlen($text) - strlen($backend);
            $text = substr_replace($text, '</li></ul></p><p>', $pos, strlen("\n"));
        } else {
            // the end of the list is the end of the text
            $text = substr_replace($text, '</li></ul>', strlen($text), strlen("\n"));
        }
        return $text;
    }
}
