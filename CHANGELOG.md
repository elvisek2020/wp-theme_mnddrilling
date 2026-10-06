# Changelog

## 0.3.1 — 2026-10-06
### MND Drilling Core
- **Obrázky na WebP bezpečněji**: přílohy z formulářů (uploads/formidable – fotky a dokumenty uchazečů) se nepřevádějí ani při převodu starších obrázků, ani při nahrání, a v Údržbě se neukazují jako nepoužité
- **Velké fotky**: při převodu se zmenší na 2560 px (jako WordPress dělá u nových nahrání), nezmenšené originály jdou rovnou do karantény bez načítání – převod nespadne na paměti
- **Dávky pokračují samy** (stránku stačí nechat otevřenou, jde zastavit); když PHP na některém obrázku spadne, příští dávka ho přeskočí a ukáže v seznamu „Nepřevedené obrázky“ s tlačítkem Zkusit znovu
- **PNG s paletou barev**: GD je neumí uložit jako WebP a zapsal prázdný soubor – takový převod se teď nepočítá a obrázek zůstane PNG (při převodu i při nahrání)
- Ošetřená poškozená metadata velikostí obrázků; délka dávky jde změnit filtrem `mnd_core_webp_batch_seconds`

### Šablona
- Beze změny funkcí (verze srovnaná s pluginem)

## 0.3.0 — 2026-10-05
### Šablona
- **Nová šablona MND Drilling se stejným vzhledem jako původní** „Ultimate for MND Drilling & Services“: hlavička s logem a názvem, slider na titulce, zelené dlaždice divizí, podmenu sekce v rámečku, obsah 630 px, tmavá patička s Compliance Hotline a spodní lišta se sítěmi – kód je nový, bez jQuery a bez šesti jQuery knihoven
- **Všechny šablony stránek** pod stejnými názvy jako dřív (nadřazená stránka, podstránka, s položkami, lidé, volné pozice, formulář, 2 sloupce; historie, společnosti a mapa pro koncepty), přiřazení stránek zůstává
- **Katalog techniky a management**: výběr položky nahoře, medailonek pod ním, bez JavaScriptu všechny pod sebou; staré odkazy `#item-ID` fungují; u katalogu se ukážou všechny položky (dřív nejvýš 10)
- **Volné pozice** se stránkováním, detail s tlačítky „Mám zájem o tuto pozici“ a „Zpět na výpis“ (odkaz na výpis místo kroku zpět)
- **Formulář „Zájem o pozici“**: název pozice se předvyplní na serveru (i bez JavaScriptu), nahrání souborů jako zelené tlačítko „Přiložit“ (dřív rozbité úzké boxy), parametr z adresy se ošetří (stará šablona měla díru XSS)
- **Osobní dotazník**: tlačítko „Odeslat“ je vidět (dřív bílé písmo na bílém tlačítku); stránky s výchozí šablonou mají nadpis
- **Mobil**: ikony dlaždic se na displejích s vysokým rozlišením nerozpadají, výběr podstránky ukazuje aktuální stránku, spodní lišta se zalomí místo oříznutí
- **Titulka**: slider stejně jako dřív (fotka 1600 × 420, na užším displeji oříznutá), nahrazuje bxSlider; název webu je hlavním nadpisem
- **Hledání** zachová jazyk a má stránkování (dřív jen prvních 10 výsledků), stránka 404 s dlaždicemi jako cestou zpět
- **Prohlížeč obrázků** z odkazů v textu (náhrada Huge IT Lightbox), **Google Analytics 4 s cookie lištou** (náhrada MonsterInsights, ID se převezme samo), **SEO** meta značky, **česká typografie**, tisková verze
- Texty šablony jdou přeložit v *Jazyky → Překlady textů* (stávající anglické překlady zůstávají, chybějící mají výchozí angličtinu místo češtiny)
- Aktualizace z GitHub Releases, upozornění, když chybí plugin MND Drilling Core

### MND Drilling Core
- Údržba webu: MonsterInsights jde do karantény až po převzetí ID měření šablonou
- Názvy dokumentů v meta boxu bez HTML entit

### Nástroje
- `dev/setup.sh` aktivuje plugin i šablonu a nahrazené pluginy přesune do `dev/_disabled-plugins/`

## 0.2.0 — 2026-10-05
### MND Drilling Core
- **Typy obsahu ze staré šablony**: Slidy, Historie, Společnosti, Volné pozice, Dokumenty, Položky, Oblasti, Lidé a kategorie položek a skupiny společností registruje plugin se stejnými názvy a adresami – obsah přežije výměnu šablony
- **Pole místo Advanced Custom Fields**: dokumenty ke stažení u stránky (pořadí šipkami), soubor dokumentu z knihovny médií, kategorie položek, rozmezí let a země; ukládá se do stejných polí jako ACF, dokud je ACF zapnutý, pole ukazuje ono
- **Dokumenty ze Simple Fields**: stránky Reference (a koncepty) dostaly své dokumenty do pole „Dokumenty ke stažení“ – plugin Simple Fields na webu dávno není
- **Pořadí přetažením** (náhrada Simple Custom Post Order): výpisy se na webu řadí podle pořadí stejně jako dřív, v přehledech administrace jde pořadí měnit přetažením řádků
- **Kategorie volných pozic** v *Volné pozice → Kategorie a e-maily* a výběr kategorie u pozice (dřív „Ultimate Theme Settings“ ve staré šabloně), data zůstávají
- **Přihlášení**: po 5 chybných pokusech za 15 minut blokace IP na 30 minut (další dvojnásobně dlouhé), hlášky neprozradí, jestli uživatel existuje; log přihlášení v *Nástroje → Log přihlášení* a sloupec Poslední přihlášení u uživatelů (náhrada Simple Login Log)
- **Zabezpečení**: vypnuté XML-RPC a editor souborů, skrytá uživatelská jména (REST, `?author=`), bez verze WordPressu, bezpečnostní HTTP hlavičky, komentáře vypnuté
- **Obrázky jako WebP** při nahrání, převod starších v Údržbě webu
- **Sitemap** s datem změny a bez uživatelů, **/llms.txt** s přehledem stránek české i anglické verze
- **Údržba webu** (*Nástroje → Údržba webu*): databáze, největší a nepoužité soubory (soubory dokumentů a logo šablony se počítají jako použité), rozbité odkazy, úklid revizí a transientů, úklid po odebraných pluginech včetně role „Career Manager“ a starých dat Simple Fields – vše přes karanténu
- **Zdraví webu** na Nástěnce a info o serveru v patičce administrace (náhrada Server IP & Memory Usage)
- **Aktualizace** z GitHub Releases, automatické aktualizace WordPressu a pluginů podle nastavení
- Vše jde zapnout a vypnout v *Nastavení → MND Drilling Core*; funguje i nad původní šablonou

### Šablona
- Zatím kostra (beze změny funkcí, verze srovnaná s pluginem)

### Nástroje
- `dev/`: lokální WordPress v Dockeru na http://localhost:8322 ze zálohy (`setup.sh`)

## 0.1.0 — 2026-10-05
### Šablona
- **Kostra šablony MND Drilling**: hlavička, seznam modulů a záložní šablona; vzhled přijde v dalších verzích

### MND Drilling Core
- **Kostra pluginu**: hlavička s `Update URI` a seznam modulů; funkce přibývají postupně

### Vydání
- GitHub Action: po tagu `vX.Y.Z` ověří verze a syntaxi PHP, sestaví `mnddrilling.zip` a `mnddrilling-core.zip` a vytvoří Release
