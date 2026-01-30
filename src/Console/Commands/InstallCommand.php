<?php

declare(strict_types=1);

namespace AlessandroNuunes\FilamentMember\Console\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'filament-member:install';

    protected $description = 'Install the filament member package';

    public function handle(): int
    {
        $tags = [
            'filament-member-config',
            'filament-member-migrations',
            'filament-member-translations',
        ];

        foreach ($tags as $tag) {
            $this->call('vendor:publish', [
                '--tag' => $tag,
                '--force' => true,
            ]);
        }

        $this->components->info('Filament Member was installed successfully.');

        return self::SUCCESS;
    }
}
