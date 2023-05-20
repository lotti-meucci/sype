[![MIT license](https://img.shields.io/badge/license-MIT-green)](./LICENSE)


# Sype!


## Working with the project

Run `run.cmd` or `./run.sh` to build and host the project with docker.

The password of the dockerized MariaDB is located in two different files:
- [MARIADB_ROOT_PASSWORD.env](./docker/MARIADB_ROOT_PASSWORD.env) (MariaDB configuration)
- [database.json](./src/api/config/database.json) (PHP database connection configuration)

We recommend to use Visual Studio Code as code editor to work on sype.



## Abstract

_Sype!_ is a typing test client-server application for web browsers.

_Sype!_ consists of 4 components:
- **Database**: MariaDB (SQL)
- **API**: PHP (JSON)
- **Web server**: Apache HTTP Server
- **Frontend**: Angular (HTML/CSS/TS)

![App model](./sype.png)



## Database (MariaDB)

### Model
![ER model](./database.png)
_word_(**id**, text) \
_user_(**id**, nickname, hash) \
_difficulty_(**id**, description, words_n) \
_game_(**id**, user_id*, difficulty_id*, datetime, result, errors_n)
> _entity_(**primary_key**, foreign_key*, attribute)

### CRUD
| Create | Read   | Update | Delete |
|--------|--------|--------|--------|
| INSERT | SELECT | UPDATE | DELETE |

### SQL scripts
- [Initializaton script](./src/database/sype.sql)
- [Wordlist](./src/database/words.sql)



## API (PHP)

### CRUD
| Create | Read | Update | Delete |
|--------|------|--------|--------|
| PUT    | GET  | PATCH  | DELETE |
> **Non-idempotent operations**: POST

### Routes
| Route                            | Methods                 | Description                                     | Type |
|----------------------------------|-------------------------|-------------------------------------------------|------|
| /users.php?q=_pattern_           | PUT, GET, PATCH, DELETE | Create, get, modify and delete users            | JSON |
| /login.php                       | GET, POST               | Log in                                          | JSON |
| /logout.php                      | POST                    | Log out                                         | JSON |
| /pictures.php?user=_nickname_    | PUT, GET, PATCH, DELETE | Create, get, modify and delete profile pictures | PNG  |
| /difficulties.php                | GET                     | Get difficulties informations                   | JSON |
| /startGame.php                   | POST                    | Starts a new game by sending a random word list | JSON |
| /endGame.php                     | POST                    | Ends a started game                             | JSON |
| /games.php?user=_nickname_       | GET                     | Get games by user                               | JSON |
| /rankings.php?difficulty=_level_ | GET                     | Get rankings by difficulty                      | JSON |
- [Usage](./tests/)
- [Routes logic](./src/api/)
