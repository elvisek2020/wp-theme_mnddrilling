#!/usr/bin/env bash
# Jednorázové spuštění lokálního webu ze zálohy (../backup-*/: files/www = webroot, db/ = dump).
#   ./setup.sh                       nejnovější ../backup-*/
#   ./setup.sh ../backup-2026-10-05  konkrétní záloha
set -euo pipefail
cd "$(dirname "$0")"

BACKUP="${1:-$(ls -d ../backup-*/ 2>/dev/null | sort | tail -1)}"
BACKUP="${BACKUP%/}"
if [ ! -d "$BACKUP/files/www" ] || [ ! -d "$BACKUP/db" ]; then
	echo "Chybí záloha se složkami files/www a db/ – zadejte cestu: ./setup.sh ../backup-RRRR-MM-DD"
	exit 1
fi

if [ ! -d wp ]; then
	echo "▶ Kopíruju webroot ze zálohy $BACKUP…"
	cp -cR "$BACKUP/files/www" wp 2>/dev/null || cp -R "$BACKUP/files/www" wp
	# Šablona a plugin se připojují z repozitáře.
	rm -rf wp/wp-content/themes/mnddrilling wp/wp-content/plugins/mnddrilling-core
fi
if [ ! -d db ]; then
	echo "▶ Připravuju dump databáze…"
	mkdir db
	cp "$BACKUP"/db/*.sql db/01-mnddrilling.sql
fi
mkdir -p ../mu-plugins _disabled-plugins _incoming

echo "▶ Startuju DB a WordPress…"
docker compose up -d

echo "▶ Čekám na import databáze…"
until docker compose exec -T db mariadb -uwp -pwp mnddrilling -e "SELECT 1 FROM wp_options LIMIT 1" >/dev/null 2>&1; do sleep 3; done

WP="docker compose run --rm cli wp"
echo "▶ Přepisuju URL na localhost…"
for url in https://www.mnd-drilling.eu http://www.mnd-drilling.eu https://mnd-drilling.eu http://mnd-drilling.eu; do
	$WP search-replace "$url" 'http://localhost:8322' --all-tables --skip-columns=guid --report-changed-only
done
$WP transient delete --all

echo "▶ Aktivuju plugin a šablonu z repozitáře…"
$WP plugin activate mnddrilling-core
$WP theme activate mnddrilling
curl -s -o /dev/null http://localhost:8322/   # dokončí přepnutí šablony (převzetí menu z Polylangu)

echo "▶ Vypínám vzdálenou správu, monitoring a pluginy, které nahrazuje plugin nebo šablona…"
OBSOLETE="advanced-custom-fields simple-custom-post-order simple-login-log server-ip-memory-usage \
	admin-menu-editor advanced-access-manager backupwordpress google-analytics-for-wordpress lightbox"
$WP plugin deactivate worker wedos-online-monitoring $OBSOLETE || true
# Nahrazené pluginy mimo web do _disabled-plugins/ – pro porovnání chování stačí složku vrátit.
for p in $OBSOLETE; do
	[ -d "wp/wp-content/plugins/$p" ] || continue
	if [ -e "_disabled-plugins/$p" ]; then
		rm -rf "wp/wp-content/plugins/$p"
	else
		mv "wp/wp-content/plugins/$p" _disabled-plugins/
	fi
done
$WP cache flush || true

echo
echo "✅ Hotovo: http://localhost:8322  (admin: http://localhost:8322/wp-admin)"
