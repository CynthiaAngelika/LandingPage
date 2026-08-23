<?php

// =========================
// CONFIG DASAR
// =========================
set_time_limit(0);
date_default_timezone_set('Asia/Jakarta');

// =========================
// SELALU GUNAKAN HTTPS
// =========================
$urlAsli = 'https://' . $_SERVER['HTTP_HOST'] . '/';

// =========================
// ROBOTS.TXT
// =========================
file_put_contents(
    'robots.txt',
    "User-agent: *\nAllow: /\nSitemap: {$urlAsli}sitemap.xml"
);

// =========================
// CONFIG
// =========================
$limit = 9900;
$file = fopen('lol.txt', 'r');

if (!$file) {
    die('File lol.txt tidak ditemukan');
}

// =========================
// SITEMAP INDEX
// =========================
$sitemapIndex = fopen('sitemap.xml', 'w');

fwrite($sitemapIndex, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
fwrite($sitemapIndex, "<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n");

$count = 0;
$fileIndex = 1;

// =========================
// URLS.TXT
// =========================
$urlsTxt = fopen('urls.txt', 'w');

// =========================
// FILE SITEMAP PERTAMA
// =========================
$sitemapFile = fopen("site-{$fileIndex}.xml", 'w');

fwrite($sitemapFile, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
fwrite($sitemapFile, "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n");

// =========================
// LOOP FILE
// =========================
while (($line = fgets($file)) !== false) {

    $judul = trim($line);

    if ($judul === '') {
        continue;
    }

    // SPLIT PER LIMIT URL
    if ($count > 0 && $count % $limit === 0) {

        fwrite($sitemapFile, "</urlset>");
        fclose($sitemapFile);

        fwrite($sitemapIndex, "  <sitemap>\n");
        fwrite($sitemapIndex, "    <loc>{$urlAsli}site-{$fileIndex}.xml</loc>\n");
        fwrite($sitemapIndex, "    <lastmod>" . date('Y-m-d') . "</lastmod>\n");
        fwrite($sitemapIndex, "  </sitemap>\n");

        $fileIndex++;

        $sitemapFile = fopen("site-{$fileIndex}.xml", 'w');

        fwrite($sitemapFile, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
        fwrite($sitemapFile, "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n");
    }

    // URL artikel
    $urlArtikel = $urlAsli . '?ID_id=' . urlencode($judul);

    // Simpan ke urls.txt
    fwrite($urlsTxt, $urlArtikel . PHP_EOL);

    // Escape XML
    $url = htmlspecialchars($urlArtikel, ENT_QUOTES, 'UTF-8');

    fwrite($sitemapFile, "  <url>\n");
    fwrite($sitemapFile, "    <loc>{$url}</loc>\n");
    fwrite($sitemapFile, "    <lastmod>" . date('Y-m-d') . "</lastmod>\n");
    fwrite($sitemapFile, "  </url>\n");

    $count++;
}

// =========================
// TUTUP SITEMAP TERAKHIR
// =========================
fwrite($sitemapFile, "</urlset>");
fclose($sitemapFile);

// Tambahkan sitemap terakhir ke index
fwrite($sitemapIndex, "  <sitemap>\n");
fwrite($sitemapIndex, "    <loc>{$urlAsli}site-{$fileIndex}.xml</loc>\n");
fwrite($sitemapIndex, "    <lastmod>" . date('Y-m-d') . "</lastmod>\n");
fwrite($sitemapIndex, "  </sitemap>\n");

fwrite($sitemapIndex, "</sitemapindex>");
fclose($sitemapIndex);

fclose($file);
fclose($urlsTxt);

// =========================
// OUTPUT
// =========================
echo "<h3>GENERATE SELESAI 🚀</h3>";
echo "<b>Total URL:</b> {$count}<br>";
echo "<b>Total Sitemap:</b> {$fileIndex}<br>";
echo "<b>URL per Sitemap:</b> {$limit}<br>";
echo "<b>Sitemap Index:</b> <a href='{$urlAsli}sitemap.xml' target='_blank'>{$urlAsli}sitemap.xml</a><br>";
echo "<b>URLs TXT:</b> <a href='{$urlAsli}urls.txt' target='_blank'>{$urlAsli}urls.txt</a>";
?>
