<?php

return [

    'bots' => [
        'mybot' => [
            'token' => env('TELEGRAM_BOT_TOKEN'),
            'certificate_path' => env('TELEGRAM_CERTIFICATE_PATH'),
            'webhook_url' => env('TELEGRAM_WEBHOOK_URL'),
            'allowed_updates' => null,
            // Ne PAS déclarer 'commands' ici
        ],
    ],

    'default' => 'mybot',

    'async_requests' => env('TELEGRAM_ASYNC_REQUESTS', false),

    'http_client_handler' => null,

    'base_bot_url' => null,

    'resolve_command_dependencies' => true,

    // 👇 La liste principale de tes commandes
    'commands' => [
        App\Telegram\Commands\StartCommand::class,
        App\Telegram\Commands\HelpCommand::class,
        App\Telegram\Commands\FactureCommand::class,
        App\Telegram\Commands\StatsCommand::class,
        App\Telegram\Commands\StockCommand::class,
        App\Telegram\Commands\RechercheCommand::class,
        // Ajoute ici toutes tes autres commandes
    ],

    'command_groups' => [],

    'shared_commands' => [],
];
