FROM python:3.10-slim

RUN apt-get update && apt-get install -y --no-install-recommends \
    git ffmpeg build-essential \
    && rm -rf /var/lib/apt/lists/*

# Versiones exactas y emparejadas: mezclar versiones distintas de torch/torchaudio
# rompe el binario compilado de torchaudio (ABI mismatch). torchaudio>=2.9 delega
# la carga de audio a torchcodec, así que hace falta instalarlo (torchcodec 0.15
# es compatible con torch>=2.11) fijando las tres versiones juntas en un mismo
# comando para que pip las resuelva de forma consistente.
RUN pip install --no-cache-dir \
    --index-url https://download.pytorch.org/whl/cpu \
    --extra-index-url https://pypi.org/simple \
    torch==2.11.0+cpu torchaudio==2.11.0+cpu torchcodec==0.15.0+cpu

# Resto de dependencias del paquete (sin gradio ni bitsandbytes: no se usan
# para inferencia por CLI, solo agregan peso y provocan backtracking de pip).
RUN pip install --no-cache-dir \
    accelerate cached_path click "ema_pytorch>=0.5.2" datasets wandb \
    "huggingface-hub==0.32.0" jieba librosa matplotlib "num2words==0.5.13" \
    "numpy<=1.26.4" pydub "pydantic<=2.10.6" pypinyin safetensors soundfile tomli \
    torchdiffeq "tqdm>=4.65.0" transformers transformers_stream_generator vocos \
    "x_transformers>=1.31.14"

# --no-deps: ya instalamos todo lo necesario arriba, evita que pip vuelva a
# tocar las versiones de torch/torchaudio/torchcodec ya resueltas.
RUN pip install --no-cache-dir --no-deps git+https://github.com/jpgallegoar/Spanish-F5.git

ENV HF_HOME=/hf-cache
ENV HOME=/tmp
ENV MPLCONFIGDIR=/tmp/mpl
# Evita que torch._inductor llame a getpass.getuser(), que falla cuando el
# contenedor corre con --user UID:GID y ese UID no existe en /etc/passwd.
ENV TORCHINDUCTOR_CACHE_DIR=/tmp/torchinductor
WORKDIR /work
ENTRYPOINT ["f5-tts_infer-cli"]
