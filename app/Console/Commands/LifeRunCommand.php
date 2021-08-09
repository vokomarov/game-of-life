<?php

namespace App\Console\Commands;

use App\Life\Universe;
use Illuminate\Console\Command;

class LifeRunCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "life:run";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Launch the Game Of Life";

    /**
     * Execute the console command.
     */
    public function handle(Universe $universe)
    {
        $universe->setSize(25, 25);
        $universe->setInitialPattern([
            [13, 12],
            [14, 13],
            [12, 14],
            [13, 14],
            [14, 14],
        ]);
        $universe->init();

        foreach ($universe->cells() as $data) {
            /** @var \App\Life\Cell $cell */
            [$x, $y, $cell] = $data;

            if ($cell->isLive()) {
                dump($x, $y, $cell->isLive());
            }
        }
    }
}
