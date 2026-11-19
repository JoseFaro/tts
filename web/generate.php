<?php
require __DIR__ . '/voices.php';

set_time_limit(0); // F5-TTS puede tardar varios minutos en CPU

header('Content-Type: application/json; charset=utf-8');

$raw = file_get_contents('php://input');
$body = json_decode($raw, true) ?? [];

$text = trim((string)($body['text'] ?? ''));
$voice = (string)($body['voice'] ?? '');
$engine = (string)($body['engine'] ?? 'piper');

if ($text === '') {
    http_response_code(400);
    echo json_encode(['error' => 'El texto está vacío.']);
    exit;
}

if (mb_strlen($text) > 3000) {
    http_response_code(400);
    echo json_encode(['error' => 'El texto es demasiado largo (máximo 3000 caracteres).']);
    exit;
}

if (!in_array($engine, ['piper', 'f5'], true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Motor no válido.']);
    exit;
}

$projectDir = realpath(__DIR__ . '/..');
$audioDir = __DIR__ . '/audio';

if (!is_dir($audioDir)) {
    mkdir($audioDir, 0775, true);
}

$filename = 'tts_' . date('Ymd_His') . '_' . substr(bin2hex(random_bytes(4)), 0, 8) . '.wav';
$uid = posix_getuid() . ':' . posix_getgid();

$start = microtime(true);

if ($engine === 'piper') {
    if (!isValidVoice($voice)) {
        http_response_code(400);
        echo json_encode(['error' => 'Voz no válida.']);
        exit;
    }

    $voicesDir = $projectDir . '/voices';
    $outputPath = $audioDir . '/' . $filename;

    $cmd = [
        'docker', 'run', '--rm', '-i',
        '--user', $uid,
        '-v', "{$voicesDir}:/voices:ro",
        '-v', "{$audioDir}:/output",
        'piper-tts',
        '-m', "/voices/{$voice}.onnx",
        '-f', "/output/{$filename}",
    ];

    $result = runProcess($cmd, $text);
} else {
    $f5Dir = $projectDir . '/f5tts';
    $referenceDir = $f5Dir . '/reference';
    $modelDir = $f5Dir . '/model';
    $hfCacheDir = $f5Dir . '/hf-cache';

    $tmpOutDir = $audioDir . '/tmp_' . bin2hex(random_bytes(6));
    mkdir($tmpOutDir, 0775, true);

    $refText = trim(file_get_contents($referenceDir . '/ref_text.txt'));

    $cmd = [
        'docker', 'run', '--rm',
        '--user', $uid,
        '-v', "{$referenceDir}:/reference:ro",
        '-v', "{$modelDir}:/model:ro",
        '-v', "{$tmpOutDir}:/output",
        '-v', "{$hfCacheDir}:/hf-cache",
        'f5-tts-es',
        '--model', 'F5-TTS',
        '--ckpt_file', '/model/model_1200000.safetensors',
        '--vocab_file', '/model/vocab.txt',
        '--ref_audio', '/reference/ref_audio.wav',
        '--ref_text', $refText,
        '--gen_text', $text,
        '--output_dir', '/output',
    ];

    $result = runProcess($cmd, null);

    $generatedFile = $tmpOutDir . '/infer_cli_out.wav';
    $outputPath = $audioDir . '/' . $filename;

    if ($result['exitCode'] === 0 && is_file($generatedFile)) {
        rename($generatedFile, $outputPath);
    }
    @unlink($generatedFile);
    @rmdir($tmpOutDir);
}

$elapsed = round(microtime(true) - $start, 2);

if ($result['exitCode'] !== 0 || !is_file($outputPath)) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Falló la generación del audio.',
        'detail' => $result['stderr'],
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'url' => 'audio/' . $filename,
    'filename' => $filename,
    'elapsed' => $elapsed,
]);

function runProcess(array $cmd, ?string $stdin): array
{
    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $process = proc_open($cmd, $descriptors, $pipes);

    if (!is_resource($process)) {
        return ['exitCode' => -1, 'stderr' => 'No se pudo iniciar el proceso.'];
    }

    if ($stdin !== null) {
        fwrite($pipes[0], $stdin);
    }
    fclose($pipes[0]);

    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);

    $exitCode = proc_close($process);

    return ['exitCode' => $exitCode, 'stderr' => $stderr];
}
