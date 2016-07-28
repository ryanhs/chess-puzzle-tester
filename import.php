<?php
require __DIR__.'/vendor/autoload.php';

use Ryanhs\Chess\Chess;
use Main\DB;

$puzzles = json_decode(file_get_contents('puzzles.json'));

$chess = new Chess();
$lastFen = '';

foreach ($puzzles as $puzzle) {
    $chess->load($puzzle->fen);
    if ($chess->fen() == $lastFen || $chess->fen() != $puzzle->fen) {
        echo "Error on : ".$puzzle->name.PHP_EOL;
        break;
    }


    if (DB::alreadyHavePuzzle($puzzle->fen)) {
        // echo str_repeat("=", 40).PHP_EOL;
        // echo "Name\t: ".$puzzle->name.PHP_EOL;
        // echo "Inserted to Database...".PHP_EOL;
    }else {
        echo str_repeat("=", 40).PHP_EOL;
        echo "Name\t: ".$puzzle->name.PHP_EOL;
        echo "Answer\t:".$puzzle->answer.PHP_EOL;
        echo $chess->ascii().PHP_EOL;

        DB::insert('puzzles', [
            'name' => $puzzle->name,
            'fen' => $puzzle->fen,
            'answer' => $puzzle->answer,
        ]);
    }

    $lastFen = $chess->fen();
}
