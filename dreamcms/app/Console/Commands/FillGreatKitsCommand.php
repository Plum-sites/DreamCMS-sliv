<?php

namespace App\Console\Commands;

use App\Models\Kit;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;

class FillGreatKitsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'new_kits:fill {branch} {dir}';

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
        $dir = $this->argument('dir');

        foreach (array_diff(scandir($dir), array('..', '.')) as $file){
            $name = str_replace('.yml', '', $file);

            $file = file_get_contents($dir . '/' . $file);
            $kit = Kit::where('server_name', '=', strtolower($name))->first();

            if ($kit){
                $config = Yaml::parse($file);

                $new_items = [];

                // Удаление

                foreach ($kit->items as $key => $items){
                    if ($items['server'] != $branch){
                        $new_items[] = $items;
                    }
                }

                // Добавление

                $kit_items = [];

                foreach ($config['Inventory']['Main'] as $item){
                    if ($item && $item != 'null'){
                        $kit_items[] = [
                            'item' => $item['type'] . (isset($item['damage']) ? (':' . $item['damage']) : ''),
                            'count' => isset($item['amount']) ? $item['amount'] : 1
                        ];
                    }
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
