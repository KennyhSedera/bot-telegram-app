<?php

namespace App\Services;

use Telegram\Bot\Laravel\Facades\Telegram;

class CallbackService
{
    public function handleCallback(array $callback)
    {
        $chatId = $callback['message']['chat']['id'] ?? null;
        $callbackData = $callback['data'] ?? '';
        $callbackQueryId = $callback['id'] ?? null;

        if (!$chatId || !$callbackData || !$callbackQueryId) {
            return;
        }

        $commandName = ltrim($callbackData, '/');

        // Call the appropriate method based on callback data
        switch ($commandName) {
            case 'facture':
                $this->handleFacture($chatId);
                break;

            case 'stats':
                $this->handleStats($chatId);
                break;

            case 'stock':
                $this->handleStock($chatId);
                break;

            case 'recherche':
                $this->handleRecherche($chatId);
                break;

            default:
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Commande inconnue.",
                ]);
                break;
        }

        Telegram::answerCallbackQuery([
            'callback_query_id' => $callbackQueryId,
        ]);
    }

    private function handleFacture($chatId)
    {
        // You can add more complex logic here
        // Like fetching data from database, etc.
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => '📄 Voici vos devis/factures',
        ]);
    }

    private function handleStats($chatId)
    {
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => '📊 Voici vos statistiques',
        ]);
    }

    private function handleStock($chatId)
    {
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => '📦 Voici votre stock',
        ]);
    }

    private function handleRecherche($chatId)
    {
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => '🔍 Fonction de recherche',
        ]);
    }
}
