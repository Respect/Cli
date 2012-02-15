From the project root, run like this:

`php bin/respect.php --../examples/config.ini now format Y-m-d`

or

`php bin/respect.php --DateTime format Y-m-d`

Params already work:

`php bin/respect.php --DateTime --time=yesterday format Y-m-d`

This doesn't work yet:

`php bin/respect.php --Pdo --dsn='sqlite::memory:' query --statement="SELECT * FROM sqlite_master" fetchAll`


