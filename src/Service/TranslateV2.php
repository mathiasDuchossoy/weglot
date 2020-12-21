<?php


namespace App\Service;


use Google\Cloud\Translate\V2\TranslateClient;

class TranslateV2
{

    /**
     * @var TranslateClient
     */
    private $client;

    public function __construct()
    {
        $this->client = new TranslateClient([
            'key' => '',
        ]);
    }

    public function translate(string $sentence, string $sourceLanguage, string $targetLanguage, array $glossary = null)
    {
        foreach ($glossary as $item) {
            $key = array_key_first($item);
            $sentence = str_replace($key, '<span class="notranslate">' . $key . '</span>', $sentence);
        }

        $result = $this->client->translate($sentence, [
            'source' => $sourceLanguage,
            'target' => $targetLanguage,
        ]);

        $translatedText = $result['text'];
        foreach ($glossary as $item) {
            $key = array_key_first($item);
            $translatedText = str_replace('<span class="notranslate">' . $key . '</span>', $item[$key], $translatedText);
        }

        return [$translatedText];
    }

    /**
     * Return bool to know if language pair is supported.
     * @param string $sourceLanguage
     * @param string $targetLanguage
     * @return bool
     */
    public function isSupported(string $sourceLanguage, string $targetLanguage)
    {
        $languages = $this->client->languages();
        return in_array($sourceLanguage, $languages) && in_array($targetLanguage, $languages);
    }
}
