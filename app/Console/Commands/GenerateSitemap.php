<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SitemapService;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate and write public/sitemap.xml for Google Search Console';

    public function handle()
    {
        $this->info('Generating sitemap.xml...');
        $success = SitemapService::writeToFile();

        if ($success) {
            $this->info('sitemap.xml successfully generated and saved to public/sitemap.xml');
            return Command::SUCCESS;
        }

        $this->error('Failed to write sitemap.xml to disk.');
        return Command::FAILURE;
    }
}
