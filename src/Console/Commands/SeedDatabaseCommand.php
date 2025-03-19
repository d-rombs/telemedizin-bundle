<?php

namespace Telemedizin\TelemedizinBundle\Console\Commands;

use Illuminate\Console\Command;
use Telemedizin\TelemedizinBundle\Database\Seeders\DatabaseSeeder;

class SeedDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telemedizin:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the database with test data for telemedizin bundle';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding database with telemedizin test data...');

        $seeder = new DatabaseSeeder();
        $seeder->run();

        $this->info('Database seeded successfully!');

        return Command::SUCCESS;
    }
} 