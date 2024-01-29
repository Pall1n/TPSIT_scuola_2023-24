<?php
$dirs = glob("*php", GLOB_ONLYDIR);

usort($dirs, function ($a, $b) {
    $a = explode(" ", $a);
    $b = explode(" ", $b);
    return strtotime($a[1]) - strtotime($b[1]);
});

$dirs = array_reverse($dirs);
?>

<!DOCTYPE html>
<html lang="it" data-theme="cupcake">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PHP Paolo Larosa</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.6.0/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-base-300 h-screen">
    <nav class="navbar shadow-lg px-5 bg-base-100 rounded-lg">
        <div class="navbar-start">
            <a href="https://github.com/Pall1n/TPSIT_scuola_2023-24" target="_blank" class="btn btn-ghost btn-circle btn-outline">
                <svg class="fill-current w-8 h-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 98 96">
                    <path d="M48.854 0C21.839 0 0 22 0 49.217c0 21.756 13.993 40.172 33.405 46.69 2.427.49 3.316-1.059 3.316-2.362 0-1.141-.08-5.052-.08-9.127-13.59 2.934-16.42-5.867-16.42-5.867-2.184-5.704-5.42-7.17-5.42-7.17-4.448-3.015.324-3.015.324-3.015 4.934.326 7.523 5.052 7.523 5.052 4.367 7.496 11.404 5.378 14.235 4.074.404-3.178 1.699-5.378 3.074-6.6-10.839-1.141-22.243-5.378-22.243-24.283 0-5.378 1.94-9.778 5.014-13.2-.485-1.222-2.184-6.275.486-13.038 0 0 4.125-1.304 13.426 5.052a46.97 46.97 0 0 1 12.214-1.63c4.125 0 8.33.571 12.213 1.63 9.302-6.356 13.427-5.052 13.427-5.052 2.67 6.763.97 11.816.485 13.038 3.155 3.422 5.015 7.822 5.015 13.2 0 18.905-11.404 23.06-22.324 24.283 1.78 1.548 3.316 4.481 3.316 9.126 0 6.6-.08 11.897-.08 13.526 0 1.304.89 2.853 3.316 2.364 19.412-6.52 33.405-24.935 33.405-46.691C97.707 22 75.788 0 48.854 0z" />
                </svg>
            </a>
        </div>
        <div class="navbar-center mx-5">
            <h1 class="text-xl font-bold">PHP Paolo Larosa - test3</h1>
        </div>
        <div class="navbar-end">
            <label class="swap swap-rotate">
                <input type="checkbox" class="theme-controller" value="dark" />
                <svg class="swap-on fill-current w-10 h-10" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z" />
                </svg>
                <svg class="swap-off fill-current w-10 h-10" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M5.64,17l-.71.71a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l.71-.71A1,1,0,0,0,5.64,17ZM5,12a1,1,0,0,0-1-1H3a1,1,0,0,0,0,2H4A1,1,0,0,0,5,12Zm7-7a1,1,0,0,0,1-1V3a1,1,0,0,0-2,0V4A1,1,0,0,0,12,5ZM5.64,7.05a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29,1,1,0,0,0,0-1.41l-.71-.71A1,1,0,0,0,4.93,6.34Zm12,.29a1,1,0,0,0,.7-.29l.71-.71a1,1,0,1,0-1.41-1.41L17,5.64a1,1,0,0,0,0,1.41A1,1,0,0,0,17.66,7.34ZM21,11H20a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2Zm-9,8a1,1,0,0,0-1,1v1a1,1,0,0,0,2,0V20A1,1,0,0,0,12,19ZM18.36,17A1,1,0,0,0,17,18.36l.71.71a1,1,0,0,0,1.41,0,1,1,0,0,0,0-1.41ZM12,6.5A5.5,5.5,0,1,0,17.5,12,5.51,5.51,0,0,0,12,6.5Zm0,9A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z" />
                </svg>
            </label>
        </div>
    </nav>

    <div class="container mx-auto px-5 py-10">
        <div class="<?php
                    switch (count($dirs)) {
                        case 1:
                            echo 'max-w-96 mx-auto';
                            break;
                        case 2:
                            echo 'grid grid-cols-1 sm:grid-cols-2 gap-7 mx-3 mx-auto max-w-2xl';
                            break;
                        default:
                            echo 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-7 mx-3';
                            break;
                    }
                    ?>">
            <?php
            foreach ($dirs as $dir) {
                echo '<div class="card max-w-full bg-base-100 shadow-md">
                        <div class="card-body">
                            <div class="card-actions justify-end gap-3">
                                <p class="font-bold w-min my-auto">' . str_replace(" - php", "", $dir) . '</p>
                                <a href="' . $dir . '" target="_blank" class="btn btn-square btn-sm bg-accent hover:bg-secondary my-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 stroke-accent-content hover:stroke-secondary-content" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17L17 7M17 7H8M17 7V16" /></svg>
                                </a>
                            </div>
                        </div>
                    </div>';
            }
            ?>
        </div>
    </div>

</body>

</html>