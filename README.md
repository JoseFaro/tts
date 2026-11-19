# tts

Generador de voz en español, local y sin servicios de pago. Dos motores:

| Motor | Calidad | Velocidad (CPU) | Uso |
|---|---|---|---|
| **Piper** (`es_MX-claude-high`) | Buena, algo sintética | Segundos por frase | Borradores, pruebas |
| **F5-TTS** (Spanish-F5, clona una voz de referencia) | Natural | Minutos por frase | Versión final |

## Uso

```bash
./serve.sh                                   # sitio web en http://127.0.0.1:8090 (texto → audio)
./tts.sh "Hola" salida.wav                   # una frase con Piper
./f5-batch.sh lote.json salida/              # varias frases con F5 (una sola carga del modelo)
./piper-batch.sh lote.json salida/           # varias frases con Piper
```

`lote.json`: `[{"id": "frase-01", "text": "…"}, …]` → `salida/frase-01.wav`. Las frases que ya
existen se saltan (caché). Lo usa [videomatic](https://github.com/JoseFaro/videomatic).

Requisitos: Docker, imágenes `piper-tts` y `f5-tts-es`, y los modelos (no van en el repo):
ver [docs/instalacion.md](docs/instalacion.md).
