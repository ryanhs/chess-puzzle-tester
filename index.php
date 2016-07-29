<?php
require __DIR__.'/vendor/autoload.php';

use Ryanhs\Chess\Chess;
use Main\DB;
use Main\UCI;

$engines = DB::loadEngines();
$puzzles = DB::loadPuzzles();

$chess = new Chess();

foreach ($engines as $engine) {
	foreach ($puzzles as $puzzle) {

		if (DB::getStatus($engine->id, $puzzle->id) === false) {
			echo PHP_EOL.str_repeat("=", 40).PHP_EOL;
			echo "Engine\t: #".$engine->id.' '.$engine->name.PHP_EOL;
			echo "Puzzle\t: #".$puzzle->id.' '.$puzzle->name.PHP_EOL;

			$answer = Chess::parsePgn($puzzle->answer)['moves'];
			$depth = count($answer);

			// repair
			$trueAnswer = UCI::pgn2moves($puzzle->fen, $puzzle->answer);
			$trueAnswerPgn = UCI::toPgn($trueAnswer);
			if ($puzzle->answer != $trueAnswerPgn) {
				DB::update('puzzles', $puzzle->id, [
					'answer' => $trueAnswerPgn,
				]);
				$puzzle->answer = $trueAnswerPgn;
				$depth = count($trueAnswer);
				echo "-- debug: pgn answer repaired...".PHP_EOL;
			}
			echo "Depth\t: ".$depth.PHP_EOL;

			echo PHP_EOL.'-Answer'.PHP_EOL;
			echo "True\t: ".$puzzle->answer.PHP_EOL;
			// continue;

			$engine_answers = [];
			for ($depth_plus_n = 0; $depth_plus_n <= 3; $depth_plus_n++) {
				$chess->load($puzzle->fen); // reset
				$engine_answer = '';
				echo "Depth+".$depth_plus_n."\t: ";

				// do simulation each depth + n
				$j = 1;
				$stillGood = true;
				for ($i = 0; $i < $depth; $i++) {
					if ($i % 2 == 0) {
						$replyLongAlgebraic = UCI::get_move($engine, $chess->fen(), $depth + $depth_plus_n);
						$replyAlgebraic = $chess->move([
							'from' => $replyLongAlgebraic[0],
							'to' => $replyLongAlgebraic[1],
							'promotion' => Chess::QUEEN,
						])['san'];

						if ($replyAlgebraic != $trueAnswer[$i]) {
							$stillGood = false;
							goto printAnswer;
						}
					} else {
						$replyAlgebraic = $trueAnswer[$i];
						$chess->move($replyAlgebraic);
					}

					printAnswer: // goto here if error
					if ($i % 2 == 0) {
						echo $j++.'.';
					}

					$engine_answer[] = $replyAlgebraic;
					echo $replyAlgebraic.' ';

					if (!$stillGood) {
						break;
					}
				}

				$engine_answer = UCI::toPgn($engine_answer);
				$engine_answers[] = $engine_answer;
				if ($puzzle->answer != $engine_answer) {
					// var_dump($puzzle->answer, $engine_answer);
					echo "\t << wrong!";
				}
				echo PHP_EOL;
			}


			DB::insert('engine_puzzle', [
				'engine' => $engine->id,
				'puzzle' => $puzzle->id,
				'answer_depth_plus_0' => $engine_answers[0],
				'answer_depth_plus_1' => $engine_answers[1],
				'answer_depth_plus_2' => $engine_answers[2],
				'answer_depth_plus_3' => $engine_answers[3],
			]);

			system("killall -q donna-4.0-linux-64");
		}

	}
}
