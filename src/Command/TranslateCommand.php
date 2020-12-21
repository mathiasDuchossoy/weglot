<?php

namespace App\Command;

use App\Service\OffsetEncodingAlgorithm;
use App\Service\Translate;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TranslateCommand extends Command
{
    protected static $defaultName = 'app:translate';
    /**
     * @var Translate
     */
    private $translate;

    public function __construct(Translate $translate, OffsetEncodingAlgorithm $offsetEncodingAlgorithm, string $name = null)
    {
        parent::__construct($name);
        $this->translate = $translate;
    }

    protected function configure()
    {
        $this
            ->setDescription('Test with the api google translate v3');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $getSupported = [
            ['en', 'fr', true],
            ['en', 'en', true],
            ['aa', 'ht', false],
            ['zz', 'fr', false],
            ['en', 'zz', false],
        ];

        foreach ($getSupported as $item) {
            $isSupported = $this->translate->isSupported($item[0], $item[1]);

            if ($item[2] === $isSupported) {
                $io->success('ok');
            } else {
                $io->error('ko');
            }
        }

        $getToTranslate = [
            ['Hello', 'en', 'fr', ['Salut'], []],
            ['Hello Thomas', 'en', 'fr', ['Salut Thomas'], []],
            ['Hello', 'en', 'it', ['Ciao'], []],
            ['<strong class="cl">Hello</strong>', 'en', 'it', ['<strong class="cl">Ciao</strong>'], []],
            ['Hello', 'en', 'it', ['Hello'], [['Hello' => 'Hello']]],
            ['Hello Thomas', 'en', 'fr', ['Bonjour Thomas'], [['Hello' => 'Bonjour']]],
            ['Ceci est mon nom de marque dans une phrase.', 'fr', 'en', ['This is my nom de marque in a sentence.'], [['nom de marque' => 'nom de marque']]]
        ];

        foreach ($getToTranslate as $item) {
            $sentence = $this->translate->translate($item[0], $item[1], $item[2], $item[4]);

            if ($item[3] === $sentence) {
                $io->success('ok');
            } else {
                $io->error('ko');
            }
        }

        $io->success('The test is over.');

        return Command::SUCCESS;
    }
}
