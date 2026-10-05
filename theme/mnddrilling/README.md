# Šablona MND Drilling

Vlastní šablona pro [www.mnd-drilling.eu](https://www.mnd-drilling.eu). Vizuálně vychází z původní šablony (zelené dlaždice divizí, levé podmenu, tmavá patička), kód je napsaný znovu: bez jQuery, bez build kroku, mobile first.

Zatím jde o kostru, moduly přibývají podle plánu.

## Struktura

| Cesta | Obsah |
|---|---|
| `style.css` | hlavička šablony (verze) |
| `functions.php` | konstanty `MND_VERSION` / `MND_DIR` / `MND_URI` a seznam modulů z `inc/` |
| `index.php` | záložní šablona |
