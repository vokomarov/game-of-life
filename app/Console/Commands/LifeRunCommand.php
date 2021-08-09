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
     * The Glider pattern for Universe
     *
     * @var \int[][]
     */
    protected $gliderPattern = [
        [13, 12],
        [14, 13],
        [12, 14],
        [13, 14],
        [14, 14],
    ];

    /**
     * Execute the console command.
     */
    public function handle(Universe $universe)
    {
        $universe->setSize(25, 25)
                 ->setInitialPattern($this->gliderPattern)
                 ->init();

        while (true) {
            $this->render($universe);

            $universe->nextGeneration();

            usleep(1000 * 300);
        }
    }

    /**
     * @param \App\Life\Universe $universe
     */
    protected function render(Universe $universe)
    {
        $line = 0;
        $rewind = 0;

        foreach ($universe->cells() as $data) {
            /** @var \App\Life\Cell $cell */
            [$x, $y, $cell] = $data;

            if ($line < $y) {
                // next line
                $this->output->write("\n\r");
            }

            $line = $y;

            if ($cell->isLive()) {
                $this->output->write('O');
            } else {
                $this->output->write('·');
            }

            $rewind = $x;
        }

        $this->output->write("\n\r");

        for ($i = 0; $i <= $rewind; $i++) {
            $this->output->write("\033[A");
        }
    }
}
