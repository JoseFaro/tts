# 2026-11-19 — Lotes para videomatic y documentación

- **Contexto:** el proyecto existía sin commits (motores Piper y F5, sitio web). Se prepara para
  GitHub (`JoseFaro/tts`, privado) y para que videomatic genere narraciones.
- **Modelos fuera del repo** (ADR-0002): se sacaron del *stage* (siguen en disco).
- **`f5-batch.sh` + `f5tts/batch.py`** (ADR-0003) y **`piper-batch.sh`**: misma interfaz de lote.
- **Medición:** F5 en CPU ~20 s de carga y ~5 min por frase corta (32 pasos); `NFE_STEP=16`
  disponible para borradores.
- Documentación con la misma estructura que los otros proyectos (README, CLAUDE.md, docs/).
