<?php

declare(strict_types=1);

use App\Domain\WordleFeature\WordleRules;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container
        ->parameters()
        ->set('wordle.word_length', WordleRules::WORD_LENGTH)
        ->set('wordle.max_guesses', WordleRules::MAX_GUESSES);
};
