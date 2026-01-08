<?php
// Path to the large barangay JSON file
$inputFile = __DIR__ . '/assets/refbrgy.json';
// Output directory for split files
$outputDir = __DIR__ . '/assets/barangay-by-province';

// Create output directory if it doesn't exist
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

// Load the barangay data
$data = json_decode(file_get_contents($inputFile), true);

// Group barangays by province code
$grouped = [];
foreach ($data as $barangay) {
    $provinceCode = substr($barangay['citymunCode'], 0, 4); // Adjust if province code is different
    if (!isset($grouped[$provinceCode])) {
        $grouped[$provinceCode] = [];
    }
    $grouped[$provinceCode][] = $barangay;
}

// Write each group to a separate file
foreach ($grouped as $provinceCode => $barangays) {
    $outFile = $outputDir . "/barangays-{$provinceCode}.json";
    file_put_contents($outFile, json_encode($barangays, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

echo "Split complete! Files are in $outputDir\n";
?>