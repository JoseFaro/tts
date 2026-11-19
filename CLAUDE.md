# tts — guía para agentes

Leer `docs/README.md`. Todo corre por Docker (imágenes `piper-tts` y `f5-tts-es`).

- **Los modelos no van al repositorio** (`*.safetensors`, `*.onnx`, `f5tts/model/`,
  `f5tts/hf-cache/`): pesan de 60 MB a 1.3 GB. Cómo obtenerlos: `docs/instalacion.md`.
- F5 tarda minutos por frase en CPU: generar por lotes (`f5-batch.sh`, una carga del modelo).
- Mantener la misma interfaz en `f5-batch.sh` y `piper-batch.sh` (la usa videomatic).
- Documentar cada sesión en `docs/historial/` y las decisiones en `docs/decisions/`.
- El usuario hace los commits y el push; proponer mensajes (conventional, en español).
