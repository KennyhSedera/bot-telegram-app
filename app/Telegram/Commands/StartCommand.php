<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class StartCommand extends Command
{
    protected string $name = 'start';
    protected string $description = 'Démarrer le bot';

    public function handle()
    {
        $keyboard = Keyboard::make()
            ->inline()
            ->row([
                Keyboard::inlineButton(['text' => '📄 Voir les devis', 'callback_data' => 'facture']),
                Keyboard::inlineButton(['text' => '📊 Dashboard', 'callback_data' => 'stats']),
            ])
            ->row([
                Keyboard::inlineButton(['text' => '📦 Stock', 'callback_data' => 'stock']),
                Keyboard::inlineButton(['text' => '🔍 Recherche', 'callback_data' => 'recherche']),
            ]);

        $this->replyWithMessage([
            'text' => '👋 Bienvenue dans le CRM Dargatech ! Que souhaites-tu faire ?',
            'reply_markup' => $keyboard,
        ]);
    }

}
