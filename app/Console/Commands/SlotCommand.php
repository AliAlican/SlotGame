<?php


namespace App\Console\Commands;


use Illuminate\Console\Command;

class SlotCommand extends Command
{
    protected $name = 'slot';


    //THIS IS THE CONFIGURATION SETTINGS OF THE GAME, IF YOU CHANGE THESE YOU MAY CREATE NEW GAMES SUCH AS WITH MORE
    // COLUMNS OR ROWS OR WITH DIFFERENT SYMBOLS AND DIFFERENT NUMBER OF SYMBOLS.
    const BET_VALUE = 100; //made using cents
    const NUM_ELEMENTS_RETURNED = 15;
    const NUM_COLUMNS = 5;
    const NUM_ROWS = 3;
    private $symbols = ['9', '10', 'J', 'Q', 'K', 'A', 'cat', 'dog', 'monkey', 'bird'];


    public function handle()
    {
        $array = $this->symbols;

        $randArray = $this->randomNElementArray($array, self::NUM_ELEMENTS_RETURNED);
        $board = $this->createBoard($randArray, self::NUM_COLUMNS, self::NUM_ROWS);
        $paylines = $this->searchForWinners($board);
        $totalWin = $this->calculateTotalWin($paylines);

        $data = [
            'board' => $randArray,
            'paylines' => $paylines,
            'bet_amount' => self::BET_VALUE,
            'total_win' => $totalWin
        ];

        print_r($data);
    }

    /**
     * Randomizes array such that you can take any number of elements from the Symbols pool with no size limit
     */
    private function randomNElementArray(array $symbols, int $size)
    {
        $randomArray = [];
        for ($i = 0; $i < $size; $i++) {
            $randomArray[] = $symbols[array_rand($symbols)];
        }
        return $randomArray;
    }

    private function createBoard(array $symbols, int $columns, int $rows)
    {
        $rowBoard = [];

        //  I designed the game in a way that number of items and length of rows and columns can be changed in the future
        // without breaking the game

        for ($i = 0; $i < $rows; $i++) {
            // in every row initial index is incremented by 1, then as columns move initial index is increased by the number of rows
            $getElement = $i;
            $rowBoard['row_' . $i] = [];
            for ($j = 0; $j < $columns; $j++) {
                $rowBoard['row_' . $i][$getElement] = $symbols[$getElement];
                $getElement += self::NUM_ROWS;
            }
        }

        return $rowBoard;
    }

    private function searchForWinners($rows)
    {
        $winnerPaylines = [];

//      YOU CAN UNCOMMENT LINES BELO AND MODIFY IT AS YOU WISH TO TEST THE CODE
//        $rows = [];
//        $rows[0] = [0 => 'cat', 3 => 'J', 6 => 'J', 9 => 'J', 12 => 'cat'];
//        $rows[1] = [1 => 'J', 4 => 'cat', 7 => 'cat', 10 => 'cat', 13 => 'cat'];

        foreach ($rows as $row) {
            $prevItem = reset($row);
            $countIdentical = 0;
            foreach ($row as $item) {
                if ($item == $prevItem) {
                    $countIdentical += 1;
                }
                $prevItem = $item;
            }

            if ($countIdentical >= 3) {
                $winnerPaylines[implode(' ', array_keys($row))] = $countIdentical;
            }
        }
        return $winnerPaylines;
    }

    private function calculateTotalWin($paylines)
    {
        $totalWin = 0;
        foreach ($paylines as $payline) {
            $repetitionAmount = $payline;
            if ($repetitionAmount == 3) {
                $win = self::BET_VALUE * 0.2;
            } else if ($repetitionAmount == 4) {
                $win = self::BET_VALUE * 2;
            } else {
                $win = self::BET_VALUE*10;
            }

            $totalWin += $win;
        }
        return $totalWin;
    }
}
