<?php

namespace App\Service;

/**
 * Class OffsetEncodingAlgorithm
 */
class OffsetEncodingAlgorithm
{
    /**
     * Lookup string
     */
    const CHARACTERS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * @var int
     */
    private $offset;

    /**
     * @param int $offset
     */
    public function __construct($offset = 1)
    {
        $this->offset = $offset;
    }

    /**
     * Encodes text by shifting each character (existing in the lookup string) by an offset (provided in the constructor)
     * Examples:
     *      offset = 1, input = "a", output = "b"
     *      offset = 2, input = "z", output = "B"
     *      offset = 1, input = "Z", output = "a"
     *
     * @param string $text
     * @return string
     */
    public function encode(string $text)
    {
        $textEncoding = '';
        $charactersLength = strlen(self::CHARACTERS);
        for ($i = 0, $length = strlen($text); $i < $length; $i++) {
            $position = strpos(self::CHARACTERS, $text[$i]);

            if (false === $position) {
                $textEncoding .= $text[$i];
            } else {
                $position += $this->offset;
                while ($position >= $charactersLength) {
                    $position -= $charactersLength;
                }

                $textEncoding .= self::CHARACTERS[$position];
            }
        }

        return $textEncoding;
    }

    public function setOffset($offset)
    {
        $this->offset = $offset;
    }
}
