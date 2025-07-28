<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Keyboard\Keyboard;

class RechercheCommand extends Command
{
    protected string $name = 'recherche';
    protected string $description = 'Rechercher un client ou un produit';

    public function handle()
    {
        $this->showSearchInterface();
    }

    public function handleCallback($chatId)
    {
        $this->showSearchInterface($chatId);
    }

    private function showSearchInterface($chatId = null)
    {
        // Créer un clavier avec des options de recherche
        $keyboard = Keyboard::make()
            ->inline()
            ->row([
                Keyboard::inlineButton(['text' => '👥 Rechercher client', 'callback_data' => 'search_client']),
                Keyboard::inlineButton(['text' => '📦 Rechercher produit', 'callback_data' => 'search_product']),
            ])
            ->row([
                Keyboard::inlineButton(['text' => '🔍 Recherche globale', 'callback_data' => 'search_global']),
            ])
            ->row([
                Keyboard::inlineButton(['text' => '🏠 Retour au menu', 'callback_data' => 'start']),
            ]);

        $text = "🔍 **Module de recherche**\n\n";
        $text .= "Que souhaitez-vous rechercher ?\n\n";
        $text .= "• **Client** : Rechercher par nom, email ou téléphone\n";
        $text .= "• **Produit** : Rechercher dans le catalogue\n";
        $text .= "• **Global** : Recherche dans toutes les données\n\n";
        $text .= "Ou tapez directement votre terme de recherche :";

        if ($chatId) {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown',
                'reply_markup' => $keyboard,
            ]);
        } else {
            $this->replyWithMessage([
                'text' => $text,
                'parse_mode' => 'Markdown',
                'reply_markup' => $keyboard,
            ]);
        }
    }

    /**
     * Rechercher des clients
     */
    public function searchClients($query, $chatId = null)
    {
        $clients = $this->searchClientsInDatabase($query);

        $text = "👥 **Résultats de recherche - Clients** :\n\n";
        $text .= "🔍 Recherche : \"*{$query}*\"\n\n";

        if (empty($clients)) {
            $text .= "Aucun client trouvé.";
        } else {
            foreach ($clients as $client) {
                $text .= "🔹 **{$client['nom']}**\n";
                $text .= "   📧 {$client['email']}\n";
                $text .= "   📱 {$client['telephone']}\n";
                $text .= "   💰 CA : {$client['ca_total']}€\n\n";
            }
        }

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

    /**
     * Rechercher des produits
     */
    public function searchProducts($query, $chatId = null)
    {
        $products = $this->searchProductsInDatabase($query);

        $text = "📦 **Résultats de recherche - Produits** :\n\n";
        $text .= "🔍 Recherche : \"*{$query}*\"\n\n";

        if (empty($products)) {
            $text .= "Aucun produit trouvé.";
        } else {
            foreach ($products as $product) {
                $stock_status = $product['stock'] > 0 ? '✅' : '❌';
                $text .= "🔹 **{$product['nom']}**\n";
                $text .= "   💰 Prix : {$product['prix']}€\n";
                $text .= "   📦 Stock : {$product['stock']} {$stock_status}\n";
                $text .= "   📝 Réf : {$product['reference']}\n\n";
            }
        }

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

    /**
     * Recherche globale
     */
    public function searchGlobal($query, $chatId = null)
    {
        $clients = $this->searchClientsInDatabase($query);
        $products = $this->searchProductsInDatabase($query);

        $text = "🔍 **Recherche globale** :\n\n";
        $text .= "🔍 Recherche : \"*{$query}*\"\n\n";

        // Clients
        if (!empty($clients)) {
            $text .= "👥 **Clients** (" . count($clients) . ") :\n";
            foreach (array_slice($clients, 0, 3) as $client) {
                $text .= "  • {$client['nom']} - {$client['email']}\n";
            }
            if (count($clients) > 3) {
                $text .= "  • ... et " . (count($clients) - 3) . " autres\n";
            }
            $text .= "\n";
        }

        // Produits
        if (!empty($products)) {
            $text .= "📦 **Produits** (" . count($products) . ") :\n";
            foreach (array_slice($products, 0, 3) as $product) {
                $text .= "  • {$product['nom']} - {$product['prix']}€\n";
            }
            if (count($products) > 3) {
                $text .= "  • ... et " . (count($products) - 3) . " autres\n";
            }
            $text .= "\n";
        }

        if (empty($clients) && empty($products)) {
            $text .= "Aucun résultat trouvé.";
        }

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

    /**
     * Rechercher des clients dans la base de données
     */
    private function searchClientsInDatabase($query)
    {
        // Exemple de données - remplacez par votre logique de base de données
        $allClients = [
            [
                'id' => 1,
                'nom' => 'Jean Dupont',
                'email' => 'jean.dupont@email.com',
                'telephone' => '0123456789',
                'ca_total' => 2500.00
            ],
            [
                'id' => 2,
                'nom' => 'Marie Martin',
                'email' => 'marie.martin@email.com',
                'telephone' => '0987654321',
                'ca_total' => 1800.50
            ],
            [
                'id' => 3,
                'nom' => 'Paul Durand',
                'email' => 'paul.durand@email.com',
                'telephone' => '0147258369',
                'ca_total' => 3200.00
            ]
        ];

        // Filtrer selon la recherche
        return array_filter($allClients, function($client) use ($query) {
            return stripos($client['nom'], $query) !== false ||
                   stripos($client['email'], $query) !== false ||
                   stripos($client['telephone'], $query) !== false;
        });

        // Exemple avec Eloquent :
        // return \App\Models\Client::where('nom', 'LIKE', "%{$query}%")
        //     ->orWhere('email', 'LIKE', "%{$query}%")
        //     ->orWhere('telephone', 'LIKE', "%{$query}%")
        //     ->limit(10)
        //     ->get()
        //     ->toArray();
    }

    /**
     * Rechercher des produits dans la base de données
     */
    private function searchProductsInDatabase($query)
    {
        // Exemple de données - remplacez par votre logique de base de données
        $allProducts = [
            [
                'id' => 1,
                'nom' => 'Ordinateur portable',
                'reference' => 'ORD001',
                'prix' => 899.99,
                'stock' => 5
            ],
            [
                'id' => 2,
                'nom' => 'Souris sans fil',
                'reference' => 'SOU001',
                'prix' => 29.99,
                'stock' => 25
            ],
            [
                'id' => 3,
                'nom' => 'Clavier mécanique',
                'reference' => 'CLA001',
                'prix' => 79.99,
                'stock' => 0
            ]
        ];

        // Filtrer selon la recherche
        return array_filter($allProducts, function($product) use ($query) {
            return stripos($product['nom'], $query) !== false ||
                   stripos($product['reference'], $query) !== false;
        });

        // Exemple avec Eloquent :
        // return \App\Models\Product::where('nom', 'LIKE', "%{$query}%")
        //     ->orWhere('reference', 'LIKE', "%{$query}%")
        //     ->limit(10)
        //     ->get()
        //     ->toArray();
    }
}
