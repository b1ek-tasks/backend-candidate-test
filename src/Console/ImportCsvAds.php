<?php

namespace App\Console;

use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCsvAds extends Command
{
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('import-csv-ads')
            ->setDescription('import-csv-ads command')
            ->addArgument('file');

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filename = $input->getArgument('file');
        $client = new Client();

        $res = $client->post(env('HOST', 'localhost') . '/api/load-xlsx-data', [
            'multipart' => [
                [
                    'name'     => 'xlsx',
                    'contents' => \GuzzleHttp\Psr7\Utils::tryFopen($filename, 'r')
                ]
            ]
        ]);

        echo $res->getBody();

        if ($res->getStatusCode() == 200) {
            return 0;
        }
        
        return $res->getStatusCode();
    }
}