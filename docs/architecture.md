# Arquitectura

| Pieza | Qué hace |
|---|---|
| `Dockerfile` → imagen `piper-tts` | Piper por CLI (`tts.sh`, `piper-batch.sh`). |
| `f5tts/Dockerfile` → imagen `f5-tts-es` | F5-TTS en español (Spanish-F5) con versiones fijas de torch (ver comentarios). |
| `f5tts/batch.py` + `f5-batch.sh` | Lote: carga el modelo **una vez** y genera todas las frases (`/output/<id>.wav`). Escribe cada audio completo o nada (archivo temporal + rename). `SPEED` y `NFE_STEP` ajustables. |
| `piper-batch.sh` | Misma interfaz de lote con Piper (rápido). |
| `web/` + `serve.sh` | Sitio PHP: escribir texto, elegir motor y voz, escuchar y descargar (`web/audio/`). |

Interfaz de lote (la usa videomatic): `script lote.json carpeta_salida` →
`carpeta_salida/<id>.wav`; las frases existentes se saltan.
