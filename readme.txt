VAATIMUKSET

Testattu PHP 5.5.31, pitäisi toimia millä tahansa PHP versiolla missä mukana SQLite3-tuki (oletuksena sitten 5.3.0).

ASENNUS

Kaikki tiedostot PHP:ta tukevan palvelimen alle.

Kansiolle ja db.sqlite3 tiedostolle tulee olla palvelinprosessilla luku, kirjoitus ja suoritus oikeudet, muuten tietokantaan kirjoitus ei toimi.

TIETOKANNAN NOLLAUS

rm db.sqlite3 && sqlite3 db.sqlite3 < schema.sql
