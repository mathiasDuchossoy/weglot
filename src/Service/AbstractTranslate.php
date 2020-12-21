<?php


namespace App\Service;


class AbstractTranslate
{
    public function addNoTranslateBalises(array $glossary, string $sentence): string
    {
        foreach ($glossary as $item) {
            $key = array_key_first($item);
            $sentence = str_replace($key, '<span class="notranslate">' . $key . '</span>', $sentence);
        }
        return $sentence;
    }

    public function removeNoTranslateBalises(array $glossary, string $sentence): string
    {
        foreach ($glossary as $item) {
            $key = array_key_first($item);
            $sentence = str_replace('<span class="notranslate">' . $key . '</span>', $item[$key], $sentence);
        }
        return $sentence;
    }
}
