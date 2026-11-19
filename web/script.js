const form = document.getElementById('tts-form');
const textEl = document.getElementById('text');
const voiceEl = document.getElementById('voice');
const engineEl = document.getElementById('engine');
const voiceField = document.getElementById('voice-field');
const submitBtn = document.getElementById('submit-btn');
const charCount = document.getElementById('char-count');

const statusEl = document.getElementById('status');
const estimateText = document.getElementById('estimate-text');
const progressFill = document.getElementById('progress-fill');

const resultEl = document.getElementById('result');
const player = document.getElementById('player');
const downloadLink = document.getElementById('download-link');

const errorEl = document.getElementById('error');

// Piper: calibrado con pruebas reales en CPU (~1.5s de arranque del
// contenedor más ~0.035s por palabra).
const PIPER_BASE_SECONDS = 1.5;
const PIPER_SECONDS_PER_WORD = 0.035;

// F5-TTS: calibrado con una sola prueba real en CPU (~352s para 21
// palabras). Es una aproximación gruesa, la duración real varía bastante.
const F5_BASE_SECONDS = 90;
const F5_SECONDS_PER_WORD = 12;

function countWords(text) {
    return text.trim().split(/\s+/).filter(Boolean).length;
}

function estimateSeconds(text, engine) {
    const words = countWords(text);
    if (engine === 'f5') {
        return F5_BASE_SECONDS + words * F5_SECONDS_PER_WORD;
    }
    return Math.max(1.5, PIPER_BASE_SECONDS + words * PIPER_SECONDS_PER_WORD);
}

function updateVoiceFieldVisibility() {
    voiceField.classList.toggle('hidden', engineEl.value === 'f5');
}

textEl.addEventListener('input', () => {
    charCount.textContent = textEl.value.length;
});

engineEl.addEventListener('change', updateVoiceFieldVisibility);
updateVoiceFieldVisibility();

form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const text = textEl.value.trim();
    const voice = voiceEl.value;
    const engine = engineEl.value;
    if (!text) return;

    errorEl.classList.add('hidden');
    resultEl.classList.add('hidden');
    statusEl.classList.remove('hidden');
    submitBtn.disabled = true;

    const estimate = estimateSeconds(text, engine);
    const estimateLabel = estimate >= 60
        ? `~${Math.round(estimate / 60)} min`
        : `~${estimate.toFixed(1)}s`;
    estimateText.textContent = engine === 'f5'
        ? `Generando audio con F5-TTS... estimado ${estimateLabel} (aproximado, varía según el equipo)`
        : `Generando audio... estimado ${estimateLabel}`;

    progressFill.style.transition = 'none';
    progressFill.style.width = '0%';
    // Forzar reflow para que la siguiente transición se aplique.
    void progressFill.offsetWidth;
    progressFill.style.transition = `width ${estimate}s linear`;
    progressFill.style.width = '90%';

    try {
        const res = await fetch('generate.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ text, voice, engine }),
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            throw new Error(data.error || 'Error desconocido al generar el audio.');
        }

        progressFill.style.transition = 'width 0.2s ease';
        progressFill.style.width = '100%';
        estimateText.textContent = `Listo en ${data.elapsed}s`;

        setTimeout(() => {
            statusEl.classList.add('hidden');
            resultEl.classList.remove('hidden');
            player.src = data.url;
            downloadLink.href = data.url;
            downloadLink.download = data.filename;
        }, 300);
    } catch (err) {
        statusEl.classList.add('hidden');
        errorEl.textContent = err.message;
        errorEl.classList.remove('hidden');
    } finally {
        submitBtn.disabled = false;
    }
});
