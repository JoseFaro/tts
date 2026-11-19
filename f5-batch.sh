#!/usr/bin/env bash
# Genera varios audios con F5-TTS en una sola carga del modelo (ver f5tts/batch.py).
# Uso: ./f5-batch.sh lote.json carpeta_salida [velocidad] [pasos]
#   pasos (NFE): 32 = calidad máxima (por defecto); 16 ≈ el doble de rápido, para borradores.
#   lote.json: [{"id": "frase-01", "text": "..."}, ...] → carpeta_salida/frase-01.wav
# Las frases cuyo .wav ya existe se saltan (caché). Lo usa videomatic para narrar videos.
set -euo pipefail

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BATCH="$(realpath "${1:?Falta el archivo JSON del lote}")"
OUT="$(mkdir -p "${2:?Falta la carpeta de salida}" && realpath "$2")"
SPEED="${3:-1.0}"
NFE_STEP="${4:-32}"

docker run --rm \
  --user "$(id -u):$(id -g)" \
  -e SPEED="$SPEED" -e NFE_STEP="$NFE_STEP" -e HOME=/tmp \
  -v "$DIR/f5tts/reference:/reference:ro" \
  -v "$DIR/f5tts/model:/model:ro" \
  -v "$DIR/f5tts/hf-cache:/hf-cache" \
  -v "$DIR/f5tts/batch.py:/work/batch.py:ro" \
  -v "$BATCH:/work/batch.json:ro" \
  -v "$OUT:/output" \
  --entrypoint python \
  f5-tts-es /work/batch.py
