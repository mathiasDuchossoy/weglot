<?php


namespace App\Service;


use Google\Cloud\Translate\V2\TranslateClient;

class TranslateV2 extends AbstractTranslate
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

    public function translate(string $sentence, string $sourceLanguage, string $targetLanguage, array $glossary = null): array
    {
        $sentence = $this->addNoTranslateBalises($glossary, $sentence);

        $result = $this->client->translate($sentence, [
            'source' => $sourceLanguage,
            'target' => $targetLanguage,
        ]);

        $translatedText[] = $this->removeNoTranslateBalises($glossary, $result['text']);

        return $translatedText;
    }

    /**
     * Return bool to know if language pair is supported.
     * @param string $sourceLanguage
     * @param string $targetLanguage
     * @return bool
     */
    public function isSupported(string $sourceLanguage, string $targetLanguage): bool
    {
        $languages = $this->client->languages();
        return in_array($sourceLanguage, $languages) && in_array($targetLanguage, $languages);
    }
}
