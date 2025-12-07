<?php
// No space for album covers - will replace with random gradients 

function getRandomGradient($seed = null) {
    $gradients = [
        "linear-gradient(135deg, #6db5c9, #94afc0)",
        "linear-gradient(135deg, #78bdd4, #9ed6ea)",
        "linear-gradient(135deg, #94afc0, #5b7b8d)",
        "linear-gradient(135deg, #5b7b8d, #3d5159)",
        "linear-gradient(135deg, rgba(74, 158, 255, 0.4), rgba(255, 107, 107, 0.4))",
        "linear-gradient(135deg, #ff9a9e, #fad0c4)",
        "linear-gradient(135deg, #a18cd1, #fbc2eb)",
        "linear-gradient(135deg, #fbc2eb, #a6c1ee)"
    ];

    if ($seed !== null) {
        $index = abs(crc32($seed)) % count($gradients);
        return $gradients[$index];
    }

    return $gradients[array_rand($gradients)];
}
