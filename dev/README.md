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
- Lokálně jsou vypnuté ManageWP, WEDOS monitoring, BackUpWordPress a MonsterInsights.
- Debug log: `wp/wp-content/debug.log`.
