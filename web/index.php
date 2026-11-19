<?php require __DIR__ . '/voices.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Generador de voz — Piper TTS</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<main class="container">
    <h1>Generador de voz</h1>
    <p class="subtitle">Texto a audio local con Piper, sin límites ni créditos.</p>

    <form id="tts-form">
        <label for="text">Texto</label>
        <textarea id="text" name="text" maxlength="3000" rows="6" placeholder="Escribe el texto a convertir..." required></textarea>
        <div class="char-count"><span id="char-count">0</span> / 3000</div>

        <label for="engine">Motor</label>
        <select id="engine" name="engine">
            <option value="piper">Piper — rápido (segundos)</option>
            <option value="f5">F5-TTS — más natural, lento (~5-10 min)</option>
        </select>

        <div id="voice-field">
            <label for="voice">Voz</label>
            <select id="voice" name="voice">
                <?php foreach (listVoices() as $v): ?>
                <option value="<?= htmlspecialchars($v['id']) ?>"><?= htmlspecialchars($v['label']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" id="submit-btn">Generar audio</button>
    </form>

    <div id="status" class="status hidden">
        <div class="spinner"></div>
        <p id="estimate-text"></p>
        <div class="progress-track">
            <div class="progress-fill" id="progress-fill"></div>
        </div>
    </div>

    <div id="result" class="result hidden">
        <audio id="player" controls></audio>
        <a id="download-link" class="download-btn" download>Descargar audio</a>
    </div>

    <p id="error" class="error hidden"></p>
</main>

<script src="script.js"></script>
</body>
</html>
