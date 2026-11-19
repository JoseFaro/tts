#!/usr/bin/env bash
# Levanta el sitio web del generador de voz.
# Uso: ./serve.sh [puerto]
set -euo pipefail

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PORT="${1:-8090}"

echo "Sirviendo en http://127.0.0.1:${PORT}"
php -S "127.0.0.1:${PORT}" -t "$DIR/web"
