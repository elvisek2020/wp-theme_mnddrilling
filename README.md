# MND Drilling — WordPress šablona a plugin

[![Release](https://img.shields.io/github/v/release/elvisek2020/wp-theme_mnddrilling?label=release)](https://github.com/elvisek2020/wp-theme_mnddrilling/releases/latest)
![WordPress](https://img.shields.io/badge/WordPress-6.6%2B-21759b)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4)
![License](https://img.shields.io/badge/license-GPL--2.0--or--later-blue)

Vlastní šablona a doprovodný plugin pro firemní web [www.mnd-drilling.eu](https://www.mnd-drilling.eu) (MND Drilling & Services a.s.) — rychlé, responzivní, bez jQuery, bez build kroku a bez externích služeb.

> **Stav:** ve vývoji. Plugin je hotový a funguje i nad původní šablonou, nová šablona (stejný vzhled jako původní) se připravuje.

| Balíček | Typ | Popis |
|---|---|---|
| **MND Drilling** (`theme/mnddrilling`) | šablona | vzhled webu: titulka s dlaždicemi divizí, stránky divizí s podmenu, katalog techniky, kariéra |
| **MND Drilling Core** (`plugins/mnddrilling-core`) | plugin | funkce nezávislé na šabloně: typy obsahu, bezpečnost, obrázky, údržba |

Obojí se vydává společně se stejným číslem verze a aktualizuje se přímo z GitHub Releases.

---

## Šablona MND Drilling

Vizuálně vychází z původní šablony (logo, zelená paleta, dlaždice divizí, levé podmenu, tmavá patička), kód je napsaný znovu. Podrobnosti v [`theme/mnddrilling/README.md`](theme/mnddrilling/README.md).

---

## Plugin MND Drilling Core

Nahrazuje 4 dřívější pluginy (Simple Login Log, Server IP & Memory Usage, Advanced Custom Fields, Simple Custom Post Order) a typy obsahu ze staré šablony. Funguje nezávisle na šabloně, každou funkci jde vypnout v *Nastavení → MND Drilling Core*.

| Oblast | Co dělá |
|---|---|
| Obsah | typy obsahu Slidy, Historie, Společnosti, Volné pozice, Dokumenty, Položky, Oblasti a Lidé a jejich kategorie – stejné názvy a adresy jako dřív |
| Pole | dokumenty ke stažení u stránky, soubor dokumentu, kategorie položek, rozmezí let a země – ve stejných polích jako ACF, data zůstávají |
| Pořadí | řazení podle pořadí na webu a přetahování řádků v přehledech administrace |
| Volné pozice | kategorie s kontaktními e-maily (*Volné pozice → Kategorie a e-maily*) a výběr kategorie u pozice |
| Přihlášení | omezení pokusů o přihlášení, log přihlášení (*Nástroje → Log přihlášení*), poslední přihlášení u uživatelů |
| Bezpečnost | vypnuté XML-RPC a editor souborů, skrytá uživatelská jména a verze WordPressu, bezpečnostní HTTP hlavičky |
| Obrázky | nahrané JPG a PNG se uloží rovnou jako WebP, převod starších obrázků v Údržbě webu |
| SEO | sitemap s datem změny a bez uživatelů, `/llms.txt` s přehledem stránek obou jazyků |
| Aktualizace | automatické aktualizace WordPressu a pluginů podle nastavení, plugin i šablona z GitHub Releases |
| Admin | informace o serveru v patičce administrace, widget Zdraví webu na Nástěnce |

**Údržba webu** — *Nástroje → Údržba webu*
- Přehled databáze (velikost tabulek, autoload) a optimalizace, úklid revizí, konceptů, koše a transientů
- Největší soubory, nepoužité obrázky a rozbité interní odkazy
- Převod starších obrázků na WebP (soubory i odkazy v obsahu, staré adresy přesměruje 301)
- Úklid po odebraných pluginech a staré šabloně: tabulky, volby, metadata, role i složky jdou nejdřív do karantény

---

## Požadavky

- WordPress 6.6+ (testováno do 7.1)
- PHP 7.4+ (doporučeno 8.x) s podporou WebP v GD nebo Imagick
- Polylang (vícejazyčnost CZ / EN)

## Instalace

1. Stáhněte `mnddrilling.zip` a `mnddrilling-core.zip` z [posledního vydání](https://github.com/elvisek2020/wp-theme_mnddrilling/releases/latest).
2. *Pluginy → Přidat nový → Nahrát plugin* → `mnddrilling-core.zip` → Aktivovat.
3. *Vzhled → Motivy → Přidat nový → Nahrát motiv* → `mnddrilling.zip` → Aktivovat.

## Aktualizace

Šablona i plugin si nové vydání najdou samy. Stačí *Nástěnka → Aktualizace → Zkontrolovat znovu* a aktualizovat.

---

## Vývoj

```
theme/mnddrilling/          šablona (podrobnosti v theme/mnddrilling/README.md)
plugins/mnddrilling-core/   plugin
mu-plugins/                 pomůcky jen pro lokální vývoj — nenasazovat
dev/                        lokální WordPress v Dockeru (dev/README.md)
tools/                      pomocné skripty
.github/workflows/          sestavení vydání
```

## Vydání nové verze

Šablona i plugin mají společnou verzi.

1. Zvyšte `Version:` v `theme/mnddrilling/style.css` **i** v `plugins/mnddrilling-core/mnddrilling-core.php` a doplňte [`CHANGELOG.md`](CHANGELOG.md).
2. Commit a anotovaný tag:
   ```bash
   git add -A && git commit -m "X.Y.Z: …"
   git status                      # musí být čisto
   git tag -a vX.Y.Z -m "X.Y.Z" && git push && git push --tags
   ```
3. GitHub Action ověří, že tag odpovídá verzím, zkontroluje syntaxi PHP, sestaví `mnddrilling.zip` a `mnddrilling-core.zip` a vytvoří Release.

## Licence

[GPL-2.0-or-later](https://www.gnu.org/licenses/gpl-2.0.html) · © Zdeněk Král ([ElvisEK](https://www.elvisek.cz))
