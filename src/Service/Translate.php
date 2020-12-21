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
    private $apiGoogleTranslateProjectId;
    private $apiGoogleTranslateLocation;
    private $apiGoogleTranslateGlossary;
    /**
     * @var string
     */
    private $glossaryPath;

    public function __construct(
        $apiGoogleTranslateCreadentialsPath,
        $apiGoogleTranslateProjectId,
        $apiGoogleTranslateLocation,
        $apiGoogleTranslateGlossary
    )
    {
        $this->client = new TranslateClient([
            'credentials' => __DIR__ . $apiGoogleTranslateCreadentialsPath,
        ]);

        $this->formattedParent = $this->client::locationName(
            $apiGoogleTranslateProjectId,
            $apiGoogleTranslateLocation
        );

        $this->glossaryPath = $this->client::glossaryName(
            $apiGoogleTranslateProjectId,
            $apiGoogleTranslateLocation,
            $apiGoogleTranslateGlossary
        );
    }

    public function translate(string $sentence, string $sourceLanguage, string $targetLanguage, array $glossary = null): array
    {
        $sentence = $this->addNoTranslateBalises($glossary, $sentence);

        try {
            $contents = [$sentence];

            $response = $this->client->getGlossary($this->glossaryPath);
            $optionArgs = [
                'sourceLanguageCode' => $sourceLanguage,
            ];

            $useGlossary = false;
            if ($sourceLanguage === $response->getLanguagePair()->getSourceLanguageCode()
                && $targetLanguage === $response->getLanguagePair()->getTargetLanguageCode()) {
                $glossaryConfig = new TranslateTextGlossaryConfig();
                $glossaryConfig->setGlossary($this->glossaryPath);
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
     */
    public function isSupported(string $sourceLanguage, string $targetLanguage): bool
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
