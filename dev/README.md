# Lokální vývoj

Požadavek: Docker (Docker Desktop / OrbStack).

```bash
cd dev
./setup.sh              # první spuštění (kopie webrootu ze zálohy, import DB, přepis URL)
docker compose up -d    # další spuštění
docker compose down     # zastavení (data zůstanou)
docker compose down -v  # smazání DB → příští setup.sh naimportuje znovu
docker compose run --rm cli wp <příkaz>   # WP-CLI
```

- Web: http://localhost:8322 (čeština `/cs/`, angličtina `/en/`)
- Šablona se vyvíjí v `../theme/mnddrilling`, plugin v `../plugins/mnddrilling-core` (připojeno do kontejneru, změny jsou vidět hned).
- mu-pluginy v `../mu-plugins` (jen lokálně, nenasazovat).
- `wp/` je kopie webrootu ze zálohy, `db/` dump databáze — obojí se neverzuje.
- `setup.sh` aktivuje plugin a šablonu z repozitáře, vypne ManageWP a WEDOS monitoring a pluginy, které nahrazuje plugin nebo šablona, přesune je do `_disabled-plugins/` (pro porovnání stačí složku vrátit do `wp/wp-content/plugins/`).
- Testovací účet správce si vytvoř přes WP-CLI (`wp user create … --role=administrator`), přístup ukládej do `_incoming/` (neverzuje se).
- Debug log: `wp/wp-content/debug.log`.
