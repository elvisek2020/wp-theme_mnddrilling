# Changelog

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
