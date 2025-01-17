<?php
$dir = "C:/Users/Victor/source/repos/App/bkp/_saomigueldeilheus/videos/";
if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            echo "filename: $file<br>";
        }
        closedir($dh);
    }
}
?>
