<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class StockCommand extends Command
{
    protected string $name = 'stock';
    protected string $description = 'Afficher le stock';

    public function handle()
    {
        $this->showStock();
    }

    public function handleCallback($chatId)
    {
        $this->showStock($chatId);
    }

    private function showStock($chatId = null)
    {
        $stock = $this->getStockFromDatabase();

        $text = "📦 **État du stock** :\n\n";

        foreach ($stock as $item) {
            $status = $item['quantity'] > 10 ? '✅' : ($item['quantity'] > 0 ? '⚠️' : '❌');
            $text .= "{$status} {$item['name']} : {$item['quantity']} unités\n";
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

    private function getStockFromDatabase()
    {
        return [
            ['name' => 'Produit A', 'quantity' => 25],
            ['name' => 'Produit B', 'quantity' => 8],
            ['name' => 'Produit C', 'quantity' => 0],
            ['name' => 'Produit D', 'quantity' => 150]
        ];
    }
}
