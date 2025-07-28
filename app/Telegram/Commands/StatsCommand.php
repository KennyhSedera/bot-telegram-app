<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class StatsCommand extends Command
{
    protected string $name = 'stats';
    protected string $description = 'Afficher les statistiques';

    public function handle()
    {
        $this->showStats();
    }

    public function handleCallback($chatId)
    {
        $this->showStats($chatId);
    }

    private function showStats($chatId = null)
    {
        $stats = $this->getStatsFromDatabase();

        $text = "📊 **Dashboard - Statistiques** :\n\n";
        $text .= "💰 Chiffre d'affaires : {$stats['ca']}€\n";
        $text .= "📄 Devis en cours : {$stats['devis_en_cours']}\n";
        $text .= "✅ Factures payées : {$stats['factures_payees']}\n";
        $text .= "👥 Nouveaux clients : {$stats['nouveaux_clients']}\n";

        if ($chatId) {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown'
            ]);
        } else {
            $this->replyWithMessage([
                'text' => $text,
                'parse_mode' => 'Markdown'
            ]);
        }
    }

    private function getStatsFromDatabase()
    {
        return [
            'ca' => 15750.50,
            'devis_en_cours' => 5,
            'factures_payees' => 12,
            'nouveaux_clients' => 3
        ];
    }
}
