<?php
// split-citymun.php
// Splits refcitymun.json into smaller files by province code
$inputFile = __DIR__ . '/assets/refcitymun.json';
$outputDir = __DIR__ . '/assets/citymun-by-province';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}
$data = json_decode(file_get_contents($inputFile), true);
$grouped = [];
foreach ($data as $citymun) {
    $provCode = $citymun['provCode'];
    if (!isset($grouped[$provCode])) {
        $grouped[$provCode] = [];
    }
    $grouped[$provCode][] = $citymun;
}
foreach ($grouped as $provCode => $cities) {
    $outFile = $outputDir . "/citymun-{$provCode}.json";
    file_put_contents($outFile, json_encode($cities, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
echo "Split complete! Files are in $outputDir\n";
?>
