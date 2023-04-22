# Sype!

## Database

![E/R diagram](./database.png)

**word**(<ins>id</ins>, text);\
**user**(<ins>id</ins>, nickname, hash, url_picture);\
**difficulty**(<ins>id</ins>, description, n_words);\
**game**(<ins>id</ins>, id_user*, id_difficulty*, datetime, result, n_errors);

## EXTRAS
- Random name generator (using sype.word table)
