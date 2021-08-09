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
     * @var int
     */
    protected $sizeX = 0;

    /**
     * @var int
     */
    protected $sizeY = 0;

    /**
     * An array of cell coordinates that should be alive before first generation
     *
     * @var array
     */
    protected $initialPattern = [];

    /**
     * Pass an array of arrays (coordinate pairs [x, y]) with cells that should be alive before first generation.
     *
     * @param int[][] $pattern
     * @return $this
     */
    public function setInitialPattern(array $pattern = []): Universe
    {
        $this->initialPattern = $pattern;

        return $this;
    }

    /**
     * Init a space with the given sizes.
     *
     * Warning! Previous state of space will be lost.
     *
     * @param int $height
     * @param int $width
     * @return $this
     */
    public function setSize(int $height, int $width): Universe
    {
        $this->sizeX = $width;
        $this->sizeY = $height;
        $this->space = [];

        for ($y = 0; $y < $this->sizeY; $y++) {
            $this->space[$y] = array_fill(0, $this->sizeX, null);
        }

        return $this;
    }

    /**
     * Create cell for every space point.
     *
     * @return $this
     */
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

    /**
     * Launch next generation of the universe.
     *
     * @return $this
     */
    public function nextGeneration(): Universe
    {
        $newSpace = [];

        foreach ($this->space as $y => $row) {
            $newSpace[$y] = [];

            foreach ($row as $x => $cell) {
                $newSpace[$y][$x] = clone $cell;

                $amount = $this->getLiveNeighborsAmount($x, $y);

                $newSpace[$y][$x]->live($amount);
            }
        }

        $this->space = $newSpace;

        return $this;
    }

    /**
     * Retrieve an iterator of all cells of the universe.
     *
     * Each iteration will return an array of cell coordinates and the cell itself:
     *  [x, y, Cell]
     *
     * @return \Generator
     */
    public function cells(): \Generator
    {
        foreach ($this->space as $y => $row) {
            foreach ($row as $x => $cell) {
                yield [$x, $y, $cell];
            }
        }
    }

    /**
     * Fetch cell by given coordinates or throw an exception
     *
     * @param int $x
     * @param int $y
     * @return \App\Life\Cell
     * @throw \RuntimeException
     */
    protected function getCellByCoordinates(int $x, int $y): Cell
    {
        $cell = $this->space[$y][$x] ?? null;

        if (! $cell instanceof Cell) {
            throw new \RuntimeException('Unable to get cell by coordinates because of space is not initialised or size overflow.');
        }

        return $cell;
    }

    /**
     * Check if cell by given coordinates should be alive before first generation
     *
     * @param int $x
     * @param int $y
     * @return bool
     */
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

    /**
     * Calculate amount of live neighbors around a cell by given coordinates.
     * By neighbors means any cell that placed near given cell horizontally, vertically or diagonally.
     *
     * @param int $x
     * @param int $y
     * @return int
     */
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

            if (
                $neighborCoordinateX < 0 || $neighborCoordinateY < 0 ||
                $neighborCoordinateX >= $this->sizeX || $neighborCoordinateY >= $this->sizeY
            ) {
                // skip access for the out of world cells
                continue;
            }

            if ($this->getCellByCoordinates($neighborCoordinateX, $neighborCoordinateY)->isLive()) {
                $count++;
            }
        }

        return $count;
    }
}
