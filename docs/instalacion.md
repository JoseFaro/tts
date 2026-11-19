# Instalación

Los modelos no están en el repositorio (pesan de 60 MB a 1.3 GB). Pasos en una máquina nueva:

## Piper
```bash
docker build -t piper-tts .
# Voz es_MX "claude" (high) del repositorio de voces de Piper (Hugging Face rhasspy/piper-voices):
#   voices/es_MX-claude-high.onnx  y  voices/es_MX-claude-high.onnx.json
```

## F5-TTS (español)
```bash
docker build -t f5-tts-es f5tts/
# Modelo Spanish-F5 (jpgallegoar) en f5tts/model/:
#   f5tts/model/model_1200000.safetensors  y  f5tts/model/vocab.txt
# Voz de referencia (la que se clona) en f5tts/reference/: ref_audio.wav + ref_text.txt
# f5tts/hf-cache/ se llena solo la primera vez (vocoder vocos).
```

> Verificar los enlaces exactos de descarga al montar otra máquina y anotarlos aquí.
