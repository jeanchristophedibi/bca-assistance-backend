<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ExportDb extends Command
{
    protected $signature = 'export-db
        {--file=database.sql : Nom du fichier de sortie}
        {--gzip=false : Compresser le dump en gzip}';

    protected $description = 'Exporter la base de données MySQL en toute sécurité';

    public function handle(): int
    {
        $file = storage_path('app/public/backups/' . $this->option('file'));
        $gzip = filter_var($this->option('gzip'), FILTER_VALIDATE_BOOLEAN);

        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0755, true);
        }

        $env = [
            'MYSQL_PWD' => config('database.connections.mysql.password'),
        ];

        $command = [
            'mysqldump',
            '-h', config('database.connections.mysql.host'),
            '-P', config('database.connections.mysql.port'),
            '-u', config('database.connections.mysql.username'),
            '--single-transaction',
            '--quick',
            config('database.connections.mysql.database'),
        ];

        $process = new Process($command, null, $env);
        $process->setTimeout(300);

        $process->run(function ($type, $buffer) use ($file) {
            file_put_contents($file, $buffer, FILE_APPEND);
        });

        if (!$process->isSuccessful()) {
            $this->error('❌ Échec de l’export DB');
            return Command::FAILURE;
        }

        if ($gzip) {
            $gzFile = $file . '.gz';
            file_put_contents(
                $gzFile,
                gzencode(file_get_contents($file), 9)
            );
            unlink($file);
            $file = $gzFile;
        }

        $this->info("✅ Base exportée avec succès : {$file}");

        return Command::SUCCESS;
    }
}
