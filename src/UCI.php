<?php

namespace Main;

use Ryanhs\Chess\Chess;

class UCI {

	public static function get_move($engine, $fen, $depth){
		$descriptorspec = array(
			0 => array("pipe","r"),
			1 => array("pipe","w"),
			2 => array("file",
"/tmp/error-output.txt",
"a") // stderr is a file to write to
		) ;
		$process = proc_open($engine->path, $descriptorspec, $pipes, __DIR__, null, []) ;
		$reply = NULL;

		if (is_resource($process)) {
			$reply = self::interact_with_engine($pipes[0], $pipes[1], $fen, $depth);
			fclose($pipes[0]);
			fclose($pipes[1]);
			if(isset($pipes[2])) fclose($pipes[2]);
			proc_terminate($process);
			proc_close($process);
		}

		// am sorry mate, will have to force
		if (is_resource($process)) {
			echo "-- debug: send SIGKILL to ".$engine->name.PHP_EOL;
			proc_terminate($process, 9); // SIGKILL
			proc_close($process);
		}

		return $reply;
	}

	public static function interact_with_engine($stdin, $stdout, $fen, $depth){
		fwrite($stdin, "uci\n");
		usleep(100000);
		fwrite($stdin, "ucinewgame\n");
		usleep(100000);
		fwrite($stdin, "isready\n");
		usleep(100000);
		fwrite($stdin, "position fen $fen\n");
		usleep(100000);

		fwrite($stdin, "go depth {$depth}\n");
		//~ fwrite($stdin, "go movetime $thinkingTime\n");

		$str="";
		while(true){
			usleep(1000);
			$s = fgets($stdout,8096);
			$str .= $s;
			// echo $s;
			if(strpos(' '.$s,'bestmove')){
				break;
			}
		}

		$teile = explode(" ", $s);
		$zug = $teile[1];
		$str = $zug;
		for ($i=0; $i < 4; $i++){
			$str[$i];
		}

		return [$str[0].$str[1], $str[2].$str[3]];
	}

	public static function toPgn($moves) {
		$pgn = '';
		$j = 1;
		for ($i = 0; $i < count($moves); $i++) {
			if ($i % 2 == 0) {
				$pgn .= $j++.'.';
			}

			$pgn .= $moves[$i].' ';
		}
		return substr($pgn, 0, -1);
	}


	public static function pgn2moves($fen, $pgn) {
		$chess = new Chess($fen);
		$moves = Chess::parsePgn($pgn)['moves'];

		foreach ($moves as $move) {
			$chess->move($move);
		}
		return $chess->history();
	}
}
