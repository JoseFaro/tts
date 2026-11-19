"""Genera varios audios con F5-TTS cargando el modelo UNA sola vez.

Corre dentro de la imagen f5-tts-es (ver ../f5-batch.sh). Cargar el modelo tarda más que
generar una frase en CPU: para un guion de 10-20 frases, un solo proceso ahorra minutos.

Entrada (/work/batch.json): [{"id": "frase-01", "text": "Hola..."}, ...]
Salida: /output/<id>.wav por cada frase. Las que ya existen se saltan (caché).
"""

import json
import os
import sys
import time

from f5_tts.api import F5TTS

BATCH = os.environ.get("BATCH_FILE", "/work/batch.json")
OUT_DIR = os.environ.get("OUTPUT_DIR", "/output")
SPEED = float(os.environ.get("SPEED", "1.0"))
# Pasos de difusión: 32 = calidad máxima; 16 ≈ el doble de rápido (borradores).
NFE_STEP = int(os.environ.get("NFE_STEP", "32"))


def main() -> int:
    with open(BATCH, encoding="utf-8") as f:
        items = json.load(f)
    pending = [i for i in items if not os.path.exists(os.path.join(OUT_DIR, f"{i['id']}.wav"))]
    print(f"{len(items)} frases, {len(pending)} por generar", flush=True)
    if not pending:
        return 0

    ref_text = open("/reference/ref_text.txt", encoding="utf-8").read().strip()
    started = time.time()
    tts = F5TTS(
        model_type="F5-TTS",
        ckpt_file="/model/model_1200000.safetensors",
        vocab_file="/model/vocab.txt",
    )
    print(f"modelo cargado en {time.time() - started:.1f}s", flush=True)

    for n, item in enumerate(pending, 1):
        t = time.time()
        out = os.path.join(OUT_DIR, f"{item['id']}.wav")
        tmp = out + ".tmp.wav"
        tts.infer(
            ref_file="/reference/ref_audio.wav",
            ref_text=ref_text,
            gen_text=item["text"],
            file_wave=tmp,
            speed=SPEED,
            nfe_step=NFE_STEP,
            show_info=lambda *a, **k: None,
        )
        os.replace(tmp, out)  # escribe completo o nada: una interrupción no deja audios a medias
        print(f"[{n}/{len(pending)}] {item['id']} ({time.time() - t:.1f}s)", flush=True)
    return 0


if __name__ == "__main__":
    sys.exit(main())
