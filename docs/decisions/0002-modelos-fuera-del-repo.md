# ADR-0002: Modelos fuera del repositorio

**Estado:** Aceptado · **Fecha:** 2026-11-19

`model_1200000.safetensors` pesa 1.3 GB (GitHub rechaza archivos de más de 100 MB), la voz
Piper 63 MB y la caché de Hugging Face 52 MB. Van en `.gitignore`; `docs/instalacion.md`
explica cómo obtenerlos. El repositorio es privado.
