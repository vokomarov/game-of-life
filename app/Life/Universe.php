<?php

namespace App\Life;

class Universe
{
    /**
     * A spacetime array which will contain arrays to represent a matrix.
     * An array indexes would represent a coordinates in two dimensions: X and Y which is width and height;
     * To access a cell by X and Y coordinate pass it as array getter [$y][$x]
     * (Y first because we're counting from top to right and from top to bottom).
     *
     * @var array
     */
    protected $space = [];

    /**
     * An array of cell coordinates that should be alive before first generation
     *
     * @var array
     */
    protected $initialPattern = [];

    public function __construct()
    {

    }

    public function setInitialPattern(array $pattern = []): Universe
    {
        $this->initialPattern = $pattern;

        return $this;
    }

    public function setSize(int $height, int $width): Universe
    {
        $this->space = [];

        for ($y = 0; $y < $height; $y++) {
            $this->space[$y] = array_fill(0, $width, null);
        }

        return $this;
    }

    public function init(): Universe
    {
        foreach ($this->space as $y => $row) {
            foreach ($row as $x => $cell) {
                $initialState = $this->isCoordinatesPresentInInitialPattern($x, $y);
                $this->space[$y][$x] = new Cell($initialState);
            }
        }

        return $this;
    }

    public function nextGeneration(): Universe
    {
        foreach ($this->space as $y => $row) {
            foreach ($row as $x => $cell) {
                $this->getCellByCoordinates($x, $y)
                     ->live($this->getLiveNeighborsAmount($x, $y));
            }
        }

        return $this;
    }

    public function cells()
    {
        foreach ($this->space as $y => $row) {
            foreach ($row as $x => $cell) {
                yield [$x, $y, $cell];
            }
        }
    }

    public function getCellByCoordinates(int $x, int $y): Cell
    {
        $cell = $this->space[$y][$x] ?? null;

        if (! $cell instanceof Cell) {
            throw new \RuntimeException('Unable to get cell by coordinates because of space is not initialised or size overflow.');
        }

        return $cell;
    }

    protected function isCoordinatesPresentInInitialPattern(int $x, int $y): bool
    {
        foreach ($this->initialPattern as $coordinates) {
            [$initX, $initY] = $coordinates;

            if ($x === $initX && $y === $initY) {
                return true;
            }
        }

        return false;
    }

    protected function getLiveNeighborsAmount(int $x, int $y): int
    {
        $neighborsRelatedCoordinatesPattern = [
            [-1, -1], [0, -1], [1, -1],
            [-1,  0], [0,  0], [1,  0],
            [-1,  1], [0,  1], [1,  1],
        ];

        $count = 0;

        foreach ($neighborsRelatedCoordinatesPattern as $coordinates) {
            [$xShift, $yShift] = $coordinates;

            if ($xShift === 0 && $yShift === 0) {
                // skipping center cell
                continue;
            }

            $neighborCoordinateX = $x + $xShift;
            $neighborCoordinateY = $y + $yShift;

            if ($this->getCellByCoordinates($neighborCoordinateX, $neighborCoordinateY)->isLive()) {
                $count++;
            }
        }

        return $count;
    }
}
