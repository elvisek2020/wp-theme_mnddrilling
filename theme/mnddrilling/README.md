# Šablona MND Drilling

Vlastní šablona pro [www.mnd-drilling.eu](https://www.mnd-drilling.eu). Vzhled odpovídá původní šabloně „Ultimate for MND Drilling & Services“ (2022) – logo, zelené dlaždice divizí, podmenu sekce vlevo, tmavá patička – kód je napsaný znovu: bez jQuery, bez build kroku, mobile first. Hybrid: PHP šablony + `theme.json`.

Typy obsahu (položky, dokumenty, volné pozice, lidé…), pole stránek a řazení jsou v pluginu **MND Drilling Core** – bez něj se část webu nezobrazí (šablona v administraci upozorní).

Funkce šablony (SEO, GA4 s cookie lištou, prohlížeč obrázků, sítě, typografie, texty pro Polylang, aktualizace) jsou **kanonické moduly** skillu wp-custom-theme v `inc/ekt/` a `assets/ekt/` – na webu se neupravují, mění se ve skillu (`scripts/modules.py sync`). Vzhled a projektové části jsou v ostatních souborech.

## Struktura

| Cesta | Obsah |
|---|---|
| `style.css` | hlavička šablony (verze, `Update URI` pro aktualizace) |
| `functions.php` | identita `EKT_*` a načtení modulů (kanon `inc/ekt/`, projektové `inc/*.php`, `inc/site-*.php`) |
| `theme.json` | paleta, písmo a šířky pro editor |
| `inc/ekt/` | kanonické moduly: `common` (Přizpůsobit, setup, úklid `<head>`, upozornění na chybějící plugin, migrace), `seo`, `analytics` (GA4 + cookie lišta), `lightbox`, `content-links`, `embeds`, `social`, `typography`, `polylang`, `updater` |
| `inc/setup.php` | umístění menu (`homepage-menu`, `footer-menu`), widgety patičky, velikosti obrázků – stejné názvy jako v původní šabloně |
| `inc/assets.php` | CSS a JS šablony s verzí podle data změny souboru, favicon |
| `inc/template-tags.php` | texty CZ/EN (`mnd_t`), čistý text názvu (`mnd_title_text`), ikony, logo, podmenu sekce, dokumenty ke stažení, stránkování |
| `inc/polylang.php` | přepínač jazyků, texty šablony do registru `ekt_pll()` (Jazyky → Překlady textů) |
| `inc/navigation.php` | dlaždice divizí z hlavního menu |
| `inc/forms.php` | třída `form mnd-form` na formulářích Formidable, předvyplnění pozice z `?pozice=` (na serveru) |
| `inc/customizer.php` | Přizpůsobit → MND Drilling → Obecné: interval slideru |
| `inc/site-mnddrilling.php` | nastavení kanonu pro web: výchozí odkazy YouTube a LinkedIn, texty cookie lišty a prohlížeče obrázků CZ/EN, popis pro vyhledávače z „O nás“, logo v JSON-LD |
| `template-parts/ekt/social.php` | vlastní markup ikon sítí ve spodní liště (šedá kolečka) |
| `header.php`, `footer.php` | hlavička (logo, název, hledání, jazyk, slider na titulce), patička (menu, widgety, Compliance Hotline, sítě) |
| `index.php` | titulka (dlaždice divizí) |
| `page-parent.php` | hlavní stránka divize – obsah první podstránky |
| `page-child.php` | podstránka divize – podmenu a text |
| `page-child-with-items.php` | katalog položek z kategorie vybrané u stránky |
| `page-child-people.php` | lidé (management) |
| `page-child-free-positions.php`, `single-position.php` | volné pozice a detail pozice |
| `page-child-career-form.php` | formulář „Zájem o pozici“ |
| `page-child-2cols.php` | text ve dvou sloupcích |
| `page-child-history.php`, `page-child-companies.php`, `page-map.php` | zachované šablony konceptů (zobrazí se jako podstránka) |
| `page.php`, `single.php`, `search.php`, `404.php` | ostatní stránky |
| `template-parts/section.php` | rozvržení sekce: dlaždice, podmenu, obsah |
| `template-parts/body-*.php`, `item.php` | katalog položek, lidé, volné pozice, tlačítka u pozice, medailonek |
| `template-parts/slider.php` | slider na titulce (Slidy) |
| `assets/css/` | `main.css` (proměnné nahoře včetně mostu `--ekt-*`), `editor.css`, `print.css` |
| `assets/js/` | `theme.js` (mobilní menu, podmenu, katalog, slider, tlačítko Přiložit) |
| `assets/ekt/` | skripty a funkční styly kanonických modulů (cookie lišta, lightbox, videa) |
| `assets/img/` | logo, ikony dlaždic, ikona certifikátu a Compliance Hotline |

## Nastavení

- **Dlaždice divizí**: Vzhled → Menu → „Hlavní menu CZ/EN“ (pořadí určuje ikonu a odstín zelené).
- **Menu v patičce**: Vzhled → Menu → „Menu v zápatí CZ/EN“.
- **Adresa a kontakty v patičce**: Vzhled → Widgety → „Patička – Sídlo firmy“ a „Patička – Kontakty“.
- **Slider**: Slidy (náhledový obrázek 1600 × 420 px, pořadí přetažením), interval v Přizpůsobit → MND Drilling → Obecné.
- **Texty šablony v angličtině**: Jazyky → Překlady textů → skupina „MND Drilling“.
- **Google Analytics 4**: Přizpůsobit → MND Drilling → SEO a analytika (prázdné ID = bez měření i bez cookie lišty).
- **Sociální sítě** ve spodní liště: Přizpůsobit → MND Drilling → Sociální sítě (prázdná hodnota ikonu skryje).
