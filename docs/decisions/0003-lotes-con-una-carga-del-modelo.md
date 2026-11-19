# ADR-0003: Generación por lotes con una sola carga del modelo

**Estado:** Aceptado · **Fecha:** 2026-11-19

Cada llamada de `web/generate.php` arranca un contenedor y vuelve a cargar F5. Para un guion
de videomatic (10–20 frases) eso multiplica el tiempo. `f5-batch.sh` corre `f5tts/batch.py`
con la API de F5 (`F5TTS.infer`): carga el modelo una vez y genera todas las frases,
saltando las que ya existen (caché). `piper-batch.sh` ofrece la misma interfaz.
Medido (CPU compartida): carga ~20 s; ~5 min por frase corta con 32 pasos.
