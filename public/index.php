<?php

declare(strict_types=1);
try {
    require_once __DIR__ . '/../vendor/autoload.php';

    $parsedown = new Parsedown();
    echo $parsedown->text(file_get_contents(__DIR__ . '/../README.md'));

} catch (\Throwable $exception) {
    include __DIR__ . '/500.php';
}

?>

<style>
    /*  Put all the universal styles here.  */

    /* Light mode */
    @media (prefers-color-scheme: light) {
        body {
            background-color: white;
            color: black;
        }
    }
    /* Dark mode */
    @media (prefers-color-scheme: dark) {
        body {
            background-color: black;
            color: white;
        }
    }

    body {
        /*background-image: url('background.jpg');*/
        background-attachment: fixed;
        background-position: center;
        background-size: 3840px;
    }
    @media
    (-webkit-min-device-pixel-ratio: 1.25),
    (min-resolution: 120dpi) {
        body {
            background-size: 3072px;
        }
    }
    @media
    (-webkit-min-device-pixel-ratio: 1.5),
    (min-resolution: 144dpi) {
        body {
            background-size: 2560px;
        }
    }
    @media
    (-webkit-min-device-pixel-ratio: 2),
    (min-resolution: 192dpi) {
        body {
            background-size: 1920px;
        }
    }
</style>
