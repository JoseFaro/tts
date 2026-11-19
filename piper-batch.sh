#!/usr/bin/env bash
# Genera varios audios con Piper (rápido: segundos por frase). Misma interfaz que f5-batch.sh.
# Uso: ./piper-batch.sh lote.json carpeta_salida [voz]
#   lote.json: [{"id": "frase-01", "text": "..."}, ...] → carpeta_salida/frase-01.wav
# Las frases cuyo .wav ya existe se saltan (caché). videomatic lo usa para borradores.
set -euo pipefail

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BATCH="$(realpath "${1:?Falta el archivo JSON del lote}")"
OUT="$(mkdir -p "${2:?Falta la carpeta de salida}" && realpath "$2")"
VOICE="${3:-es_MX-claude-high}"

python3 -c '
import json, sys
for item in json.load(open(sys.argv[1], encoding="utf-8")):
    print(item["id"] + "\t" + " ".join(item["text"].split()))
' "$BATCH" | while IFS=$'\t' read -r id text; do
  [ -f "$OUT/$id.wav" ] && continue
  echo "$text" | docker run --rm -i --user "$(id -u):$(id -g)" \
    -v "$DIR/voices:/voices:ro" -v "$OUT:/output" \
    piper-tts -m "/voices/${VOICE}.onnx" -f "/output/$id.wav" >/dev/null 2>&1
  echo "  $id"
done
