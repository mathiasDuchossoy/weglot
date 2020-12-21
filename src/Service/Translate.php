<?php


namespace App\Service;


use Google\Cloud\Translate\V3\SupportedLanguage;
use Google\Cloud\Translate\V3\TranslateTextGlossaryConfig;
use Google\Cloud\Translate\V3\TranslationServiceClient as TranslateClient;

class Translate extends AbstractTranslate
{
    /**
     * @var TranslateClient
     */
    private $client;
    /**
     * @var string
     */
    private $formattedParent;

    public function __construct()
    {
        $this->client = new TranslateClient([
            'credentials' => __DIR__ . '/../../config/translate/key.json',
        ]);

        $this->formattedParent = $this->client::locationName(
            'smiling-cistern-299214',
            'us-central1'
        );
    }

    public function translate(string $sentence, string $sourceLanguage, string $targetLanguage, array $glossary = null)
    {
        $sentence = $this->addNoTranslateBalises($glossary, $sentence);

        try {
            $contents = [$sentence];

            $glossaryPath = $this->client::glossaryName(
                'smiling-cistern-299214',
                'us-central1',
                'my_en_fr_glossary'
            );

            $response = $this->client->getGlossary($glossaryPath);
            $optionArgs = [
                'sourceLanguageCode' => $sourceLanguage,
            ];

            $useGlossary = false;
            if ($sourceLanguage === $response->getLanguagePair()->getSourceLanguageCode()
                && $targetLanguage === $response->getLanguagePair()->getTargetLanguageCode()) {
                $glossaryConfig = new TranslateTextGlossaryConfig();
                $glossaryConfig->setGlossary($glossaryPath);
                $glossaryConfig->setIgnoreCase(true);
                $optionArgs['glossaryConfig'] = $glossaryConfig;
                $useGlossary = true;
            }

            $result = $this->client->translateText($contents, $targetLanguage, $this->formattedParent, $optionArgs);

            $sentenceArray = [];

            $translations = $useGlossary ? $result->getGlossaryTranslations() : $result->getTranslations();
            foreach ($translations as $translation) {
                $translatedText = $translation->getTranslatedText();

                $sentenceArray[] = $this->removeNoTranslateBalises($glossary, $translatedText);
            }
        } finally {
            $this->client->close();
        }

        return $sentenceArray;
    }

    /**
     * Return bool to know if language pair is supported.
     * @param string $sourceLanguage
     * @param string $targetLanguage
     * @return bool
     */
    public function isSupported(string $sourceLanguage, string $targetLanguage)
    {
        try {
            $supportedLanguages = $this->client->getSupportedLanguages($this->formattedParent);
        } finally {
            $this->client->close();
        }
        $languages = $supportedLanguages->getLanguages()->getIterator();
        $sourceLanguageIsSupported = false;
        $targetLanguageIsSupported = false;
        /** @var SupportedLanguage $language */
        foreach ($languages as $language) {
            $languageCode = $language->getLanguageCode();
            if (!$sourceLanguageIsSupported && $sourceLanguage === $languageCode) {
                $sourceLanguageIsSupported = true;
            }
            if (!$targetLanguageIsSupported && $targetLanguage === $languageCode) {
                $targetLanguageIsSupported = true;
            }
        }

        return $sourceLanguageIsSupported && $targetLanguageIsSupported;
    }
}
