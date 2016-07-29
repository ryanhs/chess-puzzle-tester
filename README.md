# Chess Evaluation
this project created to evaluate engine behave with given puzzle


## installation
- `composer install`
- import database.sql in preffered Database
- copy dbconfig.json-example to dbconfig.json and edit the file according to your database connection
- run `php import.php` to add puzzle to database, you can add it in puzzles.json and run `php import.php` again
- edit table engines in database, maybe path of the engines
- run `php index.php` to see the result, and also result stored in engine_puzzle


###### notes
~ no duplicate data/process  
~ but currently its only support white move first :-p  
~ some code modified from https://github.com/antiproton/Web-GUI-for-stockfish-chess
