# Šablona MND Drilling

Vlastní šablona pro [www.mnd-drilling.eu](https://www.mnd-drilling.eu). Vzhled odpovídá původní šabloně „Ultimate for MND Drilling & Services“ (2022) – logo, zelené dlaždice divizí, podmenu sekce vlevo, tmavá patička – kód je napsaný znovu: bez jQuery, bez build kroku, mobile first. Hybrid: PHP šablony + `theme.json`.

Typy obsahu (položky, dokumenty, volné pozice, lidé…), pole stránek a řazení jsou v pluginu **MND Drilling Core** – bez něj se část webu nezobrazí (šablona v administraci upozorní).

## Struktura

| Cesta | Obsah |
|---|---|
| `style.css` | hlavička šablony (verze) |
| `functions.php` | konstanty `MND_VERSION` / `MND_DIR` / `MND_URI` a seznam modulů z `inc/` |
| `theme.json` | paleta, písmo a šířky pro editor |
| `inc/setup.php` | podpora šablony, umístění menu (`homepage-menu`, `footer-menu`), widgety patičky, velikosti obrázků – stejné názvy jako v původní šabloně |
| `inc/assets.php` | CSS a JS s verzí podle data změny souboru, favicon |
| `inc/cleanup.php` | bez emoji a zbytečných odkazů v `<head>` |
| `inc/template-tags.php` | texty CZ/EN (`mnd_t`), čistý text názvu (`mnd_title_text`), ikony, logo, podmenu sekce, dokumenty ke stažení, stránkování |
| `inc/polylang.php` | přeložitelné texty (Jazyky → Překlady textů), přepínač jazyků, převzetí menu po aktivaci |
| `inc/navigation.php` | dlaždice divizí z hlavního menu |
| `inc/forms.php` | třída `form mnd-form` na formulářích Formidable, předvyplnění pozice z `?pozice=` (na serveru) |
| `inc/typography.php` | české pevné mezery za předložkami a mezi číslem a jednotkou |
| `inc/seo.php` | meta description, Open Graph, JSON-LD |
| `inc/customizer.php` | Vzhled → Přizpůsobit → MND Drilling (interval slideru, GA4 ID) |
| `inc/analytics-consent.php` | GA4 s cookie lištou, převzetí ID z MonsterInsights |
| `inc/updater.php` | aktualizace z GitHub Releases |
| `inc/plugin-check.php` | upozornění, když chybí plugin MND Drilling Core |
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
| `assets/css/` | `main.css` (proměnné nahoře), `editor.css`, `print.css` |
| `assets/js/` | `theme.js` (mobilní panel, podmenu, katalog, slider, lightbox), `consent.js` (cookie lišta) |
| `assets/img/` | logo, ikony dlaždic, ikona certifikátu a Compliance Hotline |

## Nastavení

- **Dlaždice divizí**: Vzhled → Menu → „Hlavní menu CZ/EN“ (pořadí určuje ikonu a odstín zelené).
- **Menu v patičce**: Vzhled → Menu → „Menu v zápatí CZ/EN“.
- **Adresa a kontakty v patičce**: Vzhled → Widgety → „Patička – Sídlo firmy“ a „Patička – Kontakty“.
- **Slider**: Slidy (náhledový obrázek 1600 × 420 px, pořadí přetažením), interval v Přizpůsobit → MND Drilling.
- **Texty šablony v angličtině**: Jazyky → Překlady textů → skupina „MND Drilling“.
- **Google Analytics 4**: Přizpůsobit → MND Drilling → ID měření (převezme se samo z MonsterInsights).
