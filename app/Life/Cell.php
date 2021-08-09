<?php

namespace App\Life;

class Cell
{
    /**
     * State of the cell. True if alive.
     *
     * @var bool
     */
    protected bool $alive = false;

    /**
     * Cell constructor
     *
     * @param bool $isThereALife
     */
    public function __construct(bool $isThereALife = false)
    {
        $this->alive = $isThereALife;
    }

    /**
     * @return bool
     */
    public function isLive(): bool
    {
        return $this->alive;
    }

    /**
     * Process lifecycle of the cell by passing amount of neighbors.
     * A live cell can continue to live or die. A dead cell can also become alive.
     *
     * @param int $liveNeighborsAmount
     */
    public function live(int $liveNeighborsAmount)
    {
        if ($this->shouldDieByUnderpopulation($liveNeighborsAmount)) {
            $this->die();
            return;
        }

        if ($this->shouldLive($liveNeighborsAmount)) {
            $this->born();
            return;
        }

        if ($this->shouldDieByOvercrowding($liveNeighborsAmount)) {
            $this->die();
            return;
        }

        if ($this->shouldReanimateByReproduction($liveNeighborsAmount)) {
            $this->born();
        }
    }

    /**
     * Make cell alive
     *
     * @return void
     */
    protected function born()
    {
        $this->alive = true;
    }

    /**
     * Make cell dead
     *
     * @return void
     */
    protected function die()
    {
        $this->alive = false;
    }

    /**
     * Check if cell should die by underpopulation (when have less than 2 live neighbors)
     *
     * @param int $liveNeighborsAmount
     * @return bool
     */
    protected function shouldDieByUnderpopulation(int $liveNeighborsAmount): bool
    {
        if (! $this->isLive()) {
            return false;
        }

        return $liveNeighborsAmount < 2;
    }

    /**
     * Check if cell should continue to live normally (when have 2 or 3 live neighbors)
     *
     * @param int $liveNeighborsAmount
     * @return bool
     */
    protected function shouldLive(int $liveNeighborsAmount): bool
    {
        if (! $this->isLive()) {
            return false;
        }

        return $liveNeighborsAmount >= 2 && $liveNeighborsAmount <= 3;
    }

    /**
     * Check if cell should die by overcrowding (when have more than 3 live neighbors)
     *
     * @param int $liveNeighborsAmount
     * @return bool
     */
    protected function shouldDieByOvercrowding(int $liveNeighborsAmount): bool
    {
        if (! $this->isLive()) {
            return false;
        }

        return $liveNeighborsAmount > 3;
    }

    /**
     * Check if cell should become alive by reproduction (when have exactly 3 live neighbors).
     *
     * @param int $liveNeighborsAmount
     * @return bool
     */
    protected function shouldReanimateByReproduction(int $liveNeighborsAmount): bool
    {
        if ($this->isLive()) {
            return false;
        }

        return $liveNeighborsAmount === 3;
    }
}
