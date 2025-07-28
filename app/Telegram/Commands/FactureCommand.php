<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class FactureCommand extends Command
{
    protected string $name = 'facture';
    protected string $description = 'Afficher vos devis/factures';

    public function handle()
    {
        // La logique principale dans une méthode séparée
        $this->showFactures();
    }

    /**
     * Méthode pour exécuter depuis un callback
     */
    public function handleCallback($chatId)
    {
        // Utiliser la même logique que handle()
        $this->showFactures($chatId);
    }

    /**
     * Logique principale - utilisée par handle() et handleCallback()
     */
    private function showFactures($chatId = null)
    {
        // Récupérer les devis/factures depuis la base de données
        $factures = $this->getFacturesFromDatabase();

        $text = "📄 **Vos devis/factures** :\n\n";

        if (empty($factures)) {
            $text .= "Aucun devis trouvé.";
        } else {
            foreach ($factures as $facture) {
                $text .= "🔹 Devis #{$facture['id']} - {$facture['client']} - {$facture['montant']}€\n";
                $text .= "   📅 {$facture['date']} - Status: {$facture['status']}\n\n";
            }
        }

        // Si c'est un callback, envoyer directement
        if ($chatId) {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown'
            ]);
        } else {
            // Si c'est une commande normale, utiliser replyWithMessage
            $this->replyWithMessage([
                'text' => $text,
                'parse_mode' => 'Markdown'
            ]);
        }
    }

    /**
     * Récupérer les factures depuis la base de données
     */
    private function getFacturesFromDatabase()
    {
        // Exemple de données - remplacez par votre logique de base de données
        return [
            [
                'id' => 'F2025001',
                'client' => 'Client A',
                'montant' => 1250.00,
                'date' => '2025-01-15',
                'status' => 'En attente'
            ],
            [
                'id' => 'F2025002',
                'client' => 'Client B',
                'montant' => 890.50,
                'date' => '2025-01-20',
                'status' => 'Validé'
            ],
            [
                'id' => 'F2025003',
                'client' => 'Client C',
                'montant' => 2100.00,
                'date' => '2025-01-25',
                'status' => 'Payé'
            ]
        ];

        // Exemple avec Eloquent :
        // return \App\Models\Facture::latest()->limit(10)->get()->toArray();
    }
}
