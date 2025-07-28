<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramBotController extends Controller
{
    public function handle(Request $request)
    {
        // Gère les commandes /start, /facture, etc.
        Telegram::commandsHandler(true);

        $data = $request->all();
        $callback = $data['callback_query'] ?? null;
        $message = $data['message'] ?? null;

        if ($callback) {
            $this->handleCallback($callback);
        } elseif ($message && isset($message['text'])) {
            // Gérer les messages texte pour la recherche
            $this->handleTextMessage($message);
        }

        return response('ok', 200);
    }

    private function handleCallback(array $callback)
    {
        $chatId = $callback['message']['chat']['id'] ?? null;
        $callbackData = $callback['data'] ?? '';
        $callbackQueryId = $callback['id'] ?? null;

        if (!$chatId || !$callbackData || !$callbackQueryId) {
            return;
        }

        // Gérer les callbacks de recherche spéciaux
        if (str_starts_with($callbackData, 'search_')) {
            $this->handleSearchCallback($callbackData, $chatId);
            Telegram::answerCallbackQuery([
                'callback_query_id' => $callbackQueryId,
            ]);
            return;
        }

        $commandName = ltrim($callbackData, '/');

        $commandMap = [
            'facture' => \App\Telegram\Commands\FactureCommand::class,
            'stats' => \App\Telegram\Commands\StatsCommand::class,
            'stock' => \App\Telegram\Commands\StockCommand::class,
            'recherche' => \App\Telegram\Commands\RechercheCommand::class,
            'start' => \App\Telegram\Commands\StartCommand::class,
        ];

        if (!isset($commandMap[$commandName])) {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "Commande inconnue.",
            ]);
            return;
        }

        // Instancier la commande et appeler handleCallback
        $commandInstance = new $commandMap[$commandName]();
        $commandInstance->handleCallback($chatId);

        // Répondre au callback query
        Telegram::answerCallbackQuery([
            'callback_query_id' => $callbackQueryId,
        ]);
    }

    private function handleSearchCallback($callbackData, $chatId)
    {
        switch ($callbackData) {
            case 'search_client':
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => '👥 **Recherche de client**' . "\n\n" .
                             'Tapez le nom, email ou téléphone du client à rechercher :',
                    'parse_mode' => 'Markdown'
                ]);
                // Ici vous pourriez sauvegarder l'état "en attente de recherche client"
                break;

            case 'search_product':
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => '📦 **Recherche de produit**' . "\n\n" .
                             'Tapez le nom ou la référence du produit à rechercher :',
                    'parse_mode' => 'Markdown'
                ]);
                // Ici vous pourriez sauvegarder l'état "en attente de recherche produit"
                break;

            case 'search_global':
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => '🔍 **Recherche globale**' . "\n\n" .
                             'Tapez votre terme de recherche (clients et produits) :',
                    'parse_mode' => 'Markdown'
                ]);
                // Ici vous pourriez sauvegarder l'état "en attente de recherche globale"
                break;
        }
    }

    private function handleTextMessage(array $message)
    {
        $chatId = $message['chat']['id'] ?? null;
        $text = $message['text'] ?? '';

        // Ignorer les commandes (qui commencent par /)
        if (str_starts_with($text, '/')) {
            return;
        }

        // Si c'est un message texte normal, on fait une recherche globale
        if (!empty($text) && $chatId) {
            $searchCommand = new \App\Telegram\Commands\RechercheCommand();
            $searchCommand->searchGlobal($text, $chatId);
        }
    }
}
