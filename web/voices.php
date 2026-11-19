<?php
// Escanea la carpeta voices/ y devuelve la lista de voces disponibles
// con un nombre legible armado a partir de los metadatos de cada modelo.

function listVoices(): array
{
    $voicesDir = __DIR__ . '/../voices';
    $voices = [];

    foreach (glob($voicesDir . '/*.onnx') as $onnxFile) {
        $id = basename($onnxFile, '.onnx');
        $jsonFile = $onnxFile . '.json';

        $label = $id;
        if (is_file($jsonFile)) {
            $meta = json_decode(file_get_contents($jsonFile), true);
            if (is_array($meta)) {
                $lang = $meta['language'] ?? [];
                $parts = array_filter([
                    $lang['name_english'] ?? null,
                    $lang['country_english'] ?? null,
                    $meta['dataset'] ?? null,
                    $meta['audio']['quality'] ?? null,
                ]);
                if ($parts) {
                    $label = implode(' · ', $parts);
                }
            }
        }

        $voices[] = ['id' => $id, 'label' => $label];
    }

    usort($voices, fn($a, $b) => strcmp($a['label'], $b['label']));

    return $voices;
}

// Devuelve true si $id corresponde a una voz real disponible (evita path traversal).
function isValidVoice(string $id): bool
{
    if (!preg_match('/^[A-Za-z0-9_\-]+$/', $id)) {
        return false;
    }
    return is_file(__DIR__ . "/../voices/{$id}.onnx");
}
