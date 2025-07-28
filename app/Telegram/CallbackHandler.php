<?php

namespace App\Telegram;

use Telegram\Bot\Laravel\Facades\Telegram;

class CallbackHandler
{
    public static function handle($chatId, $callbackData)
    {
        switch ($callbackData) {
            case 'facture':
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "📄 Voici la liste de vos devis...",
                ]);
                break;

            case 'stats':
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "📊 Voici vos statistiques.",
                ]);
                break;

            case 'stock':
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "📦 Voici l'état de votre stock.",
                ]);
                break;

            case 'recherche':
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "🔍 Entrez le nom du client ou produit à rechercher.",
                ]);
                break;

            default:
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Commande inconnue.",
                ]);
        }
    }
}
