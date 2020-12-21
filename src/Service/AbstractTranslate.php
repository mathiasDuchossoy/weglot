<?php


namespace App\Service;


class AbstractTranslate
{
    /**
     * Add tags to not translate the words contained in the glossary
     */
    public function addNoTranslateBalises(array $glossary, string $sentence): string
    {
        foreach ($glossary as $item) {
            $key = array_key_first($item);
            $sentence = str_replace($key, '<span class="notranslate">' . $key . '</span>', $sentence);
        }
        return $sentence;
    }

    /**
     * Remove the tags and replace the words with those from the glossary
     */
    public function removeNoTranslateBalises(array $glossary, string $sentence): string
    {
        foreach ($glossary as $item) {
            $key = array_key_first($item);
            $sentence = str_replace('<span class="notranslate">' . $key . '</span>', $item[$key], $sentence);
        }
        return $sentence;
    }
}
