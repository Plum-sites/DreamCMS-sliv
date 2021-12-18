<?php

namespace App\Console\Commands;

use App\Models\Kit;
use Illuminate\Console\Command;
use Symfony\Component\Yaml\Yaml;

class FillKitsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kits:fill {branch} {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill kits for branch';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $branch = $this->argument('branch');
        $file = file_get_contents($this->argument('file'));
        $config = Yaml::parse($file);

        foreach ($config['kits'] as $name => $info) {
            $kit = Kit::where('server_name', '=', strtolower($name))->first();

            if ($kit){
                $new_items = [];

                // Удаление

                foreach ($kit->items as $key => $items){
                    if ($items['server'] != $branch){
                        $new_items[] = $items;
                    }
                }

                // Добавление

                $kit_items = [];

                foreach ($info['items'] as $item){
                    $info = explode(' ', $item);

                    $kit_items[] = [
                        'item' => $info[0],
                        'count' => $info[1]
                    ];
                }

                $new_items[] = [
                    'server' => $branch,
                    'items' => $kit_items
                ];

                $kit->items = $new_items;
                $kit->save();
            }else{
                echo "Kit " . $name . " not found!" . PHP_EOL;
            }
        }

        return 0;
    }
}
