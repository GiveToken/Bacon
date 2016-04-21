<?php
namespace Sizzle\Bacon\Text;

/**
 * Function to convert camel case to underscore
 */
trait CamelToUnderscore
{

    /**
     * Turns a camel case input into an underscored one
     *
     * @param string $input - camel case string
     *
     * @return string - underscore string
     */
    public function fromCamelCase(string $input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }
}
