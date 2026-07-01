<?php
function asset($path) {
    $fullPath = __DIR__ . '/../public/' . $path;
    $v = file_exists($fullPath) ? filemtime($fullPath) : time();
    return 'public/' . $path . '?v=' . $v;
}
