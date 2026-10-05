# MND Drilling — WordPress šablona a plugin

[![Release](https://img.shields.io/github/v/release/elvisek2020/wp-theme_mnddrilling?label=release)](https://github.com/elvisek2020/wp-theme_mnddrilling/releases/latest)
![WordPress](https://img.shields.io/badge/WordPress-6.6%2B-21759b)
![PHP](https://img.shields.io/badge/PHP-8.1%2B-777bb4)
![License](https://img.shields.io/badge/license-GPL--2.0--or--later-blue)

Vlastní šablona a doprovodný plugin pro firemní web [www.mnd-drilling.eu](https://www.mnd-drilling.eu) (MND Drilling & Services a.s.) — rychlé, responzivní, bez jQuery, bez build kroku a bez externích služeb.

> **Stav:** ve vývoji. Verze 0.1.0 obsahuje jen kostru šablony i pluginu, funkce přibývají postupně.

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

Postupně nahradí dřívější pluginy a funguje nezávisle na šabloně. Přehled náhrad bude doplněn s prvními moduly.

---

## Požadavky

- WordPress 6.6+ (testováno do 7.1)
- PHP 8.1+ s podporou WebP v GD nebo Imagick
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
