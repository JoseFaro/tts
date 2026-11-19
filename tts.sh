#!/usr/bin/env bash
# Uso: ./tts.sh "texto a convertir" salida.wav [voz]
# Voces disponibles en ./voices (nombre de archivo .onnx sin extensión)
set -euo pipefail

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
TEXT="${1:?Falta el texto}"
OUT="${2:-output/salida.wav}"
VOICE="${3:-es_MX-claude-high}"

mkdir -p "$DIR/output"

echo "$TEXT" | docker run --rm -i \
  --user "$(id -u):$(id -g)" \
  -v "$DIR/voices:/voices" \
  -v "$DIR/output:/output" \
  piper-tts \
  -m "/voices/${VOICE}.onnx" \
  -f "/output/$(basename "$OUT")"

echo "Generado: $DIR/output/$(basename "$OUT")"
