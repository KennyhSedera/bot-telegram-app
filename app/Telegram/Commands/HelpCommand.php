<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;

class HelpCommand extends Command
{
    protected string $name = 'help';
    protected string $description = 'Liste les commandes disponibles';

    public function handle()
    {
        $commands = $this->getTelegram()->getCommandBus()->getCommands();

        $text = "Liste des commandes disponibles :\n\n";

        foreach ($commands as $command) {
            $text .= '/' . $command->getName() . ' - ' . $command->getDescription() . "\n";
        }

        $this->replyWithMessage(['text' => $text]);
    }

}
