<?php

if (!isset($argv[1])) die("please mention directory!" . PHP_EOL);

$files = [];
$dirs = [];
$medias = ['mp4', 'avi', 'mkv', 'webm', 'wmv', 'flv'];
$realpath = $argv[1];
if (substr($realpath, -1) != '/') $realpath .= '/';
$path = $realpath . 'src';
function generate_video_preview($file, $path)
{
    $imgPath = $path . "/img";
    $img = pathinfo($file->getPathname())['filename'] . '_preview.jpg';
    if (!file_exists($imgPath . "/" . $img)) {
        echo "Generating " . $imgPath . "/" . $img . PHP_EOL;
        chdir($imgPath);
        exec(__DIR__ . "/video-preview.sh " . escapeshellarg($file->getPathname()));
    }
    return 'src/img/' . $img;
}

function update_html($file, $files, $dirs = [])
{
    file_put_contents(dirname($file) . '/directory-data.json', json_encode(compact('files', 'dirs')));
    exec("php " . __DIR__ . "/index-html.php " . escapeshellarg($file));
}


$di = new DirectoryIterator($path);
foreach ($di as $d) {
    if (!$d->isFile()) continue;
    if (in_array($d->getExtension(), $medias)) $files[] = ["src/" . $d->getFilename(), generate_video_preview($d, $path)];
    update_html($realpath . "index.html", $files);
}
foreach ([$realpath, $realpath . "../", $realpath . "../../"] as $searchdir) {
    $di2 = new DirectoryIterator($searchdir);
    foreach ($di2 as $d) {
        if ($d->isFile() || $d->isDot()) continue;
        $index = $d->getFilename() . "/index.html";
        if (!file_exists($searchdir . $d->getFilename() . "/directory-data.json")) continue;
        $dirs[] = [str_replace($realpath, '', $searchdir) . $index, $d->getFilename()];
        update_html($realpath . "index.html", $files, $dirs);
    }
}
