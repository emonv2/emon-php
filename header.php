<?php
include 'lib/core.php';

session::init();
$isLogged = session::get('login');
$isAdmin = session::get('isAdmin');
$isSystemUser = session::get('whoIs') == 'system_user' ? true : false;
$uid = session::get('id');
$core = new Core();

$adminDashboard = __DIR__ . '/adm/dashboard.php';

$color = $core->systemOption('theme_color');


if (!isset($page_title)) {
    $page_title = SITE_NAME . ' - Home';
}


// $randomTicker = $core->getRandomTicker();

function backButton($path)
{
    echo '<a href="' . $path . '" class="text-xs underline"><i class="fa-solid fa-arrow-left mr-1"></i> BACK</a>';
}

function backButtonJs()
{
    echo '<span class="text-xs underline" onclick="history.back()"><i class="fa-solid fa-arrow-left mr-1 cursor-pointer"></i> BACK</span>';
}

$bgColor = $core->systemOption('site_bg_color')->var_value;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?></title>
    <link rel="shortcut icon" href="<?= SITE_URL ?>/favicon.ico" type="image/x-icon">
    <link rel="icon" href="<?= SITE_URL ?>/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- <link rel="stylesheet" href="<?= SITE_URL ?>/lib/css/style.css"> -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<style type="text/tailwindcss">
    @theme {
        --color-emon-accent: <?php echo $color->var_value; ?>;
        --color-emon-red: "#FF0000";
        --color-emon-green: "#30fa23";
    } 
    .div-header {
    @apply bg-emon-accent font-semibold text-white py-2 md:py-4 px-3 md:px-4 uppercase flex justify-between items-center;
    }

    .div-header-bold {
    @apply bg-emon-accent text-white py-2 md:py-4 px-3 md:px-4 uppercase;
    }

    .div-normal {
    @apply py-2 md:py-4 px-3 md:px-4 uppercase;
    }

    .div-normal-border {
    @apply border-2 py-2 md:py-4 px-3 md:px-4 border-emon-accent uppercase;
    }

    .div-space {
    @apply py-2 md:py-4 px-3 md:px-4 uppercase;
    }

    .e-input {
    @apply py-1.5 px-2.5 md:py-2 md:px-3 uppercase border-2 rounded-lg w-full md:w-1/2;
    }

    .e-button {
    @apply bg-emon-accent rounded-lg px-5 py-2 text-white cursor-pointer uppercase;
    }

    .e-sort {
    @apply text-right text-xs underline;
    }
</style>

<body style="font-family: 'Roboto', sans-serif;" class="bg-[<?= $bgColor ?>]">
    <div class="flex justify-center items-center flex-col text-emon-accent">
        <div class="w-[336px] md:w-[728px] min-h-screen">

            <!-- navbar start -->
            <div class="div-header flex justify-between my-1">
                <a href="<?= SITE_URL ?>/index.php">
                    <h1 class="font-bold"><?php echo SITE_NAME ?></h1>
                </a>

                <div class="flex gap-4">

                    <?php if ($isLogged && session::get('whoIs') == 'system_user') { ?>
                        <a href="<?= SITE_URL ?>/system_user/index.php">
                            System_User
                        </a>
                    <?php } else if ($isAdmin) { ?>
                        <a href="<?= SITE_URL ?>/system/index.php">
                            Admin
                        </a>
                    <?php } ?>
                    <button id="openButton" class="block flex gap-2 items-center" onclick="openMenu()">
                        <i class="fa-solid fa-bars"></i> <span class="text-right text-xs underline">
                            MENU
                        </span>
                    </button>
                    <button id="closeButton" class="hidden flex gap-2 items-center" onclick="closeMenu()">
                        <i class="fa-solid fa-xmark"></i> <span class="text-right text-xs underline">
                            CLOSE
                        </span>
                    </button>
                </div>
            </div>

            <div id="navbar" class="hidden mb-1">
                <?php
                if ($isAdmin) { ?>
                    <a href="<?= SITE_URL ?>/system/index.php">
                        <div class="div-header font-normal mb-1 hover:bg-transparent hover:text-emon-accent">
                            Admin Portal
                        </div>
                    </a>
                    <a href="<?= SITE_URL ?>/logout.php">
                        <div class="div-header font-normal mb-1 hover:bg-transparent hover:text-emon-accent">
                            logout
                        </div>
                    </a>
                <?php } else if ($isSystemUser) { ?>
                    <a href="<?= SITE_URL ?>/system_user/index.php">
                        <div class="div-header font-normal mb-1 hover:bg-transparent hover:text-emon-accent">
                            System User Portal
                        </div>
                    </a>
                    <a href="<?= SITE_URL ?>/logout.php">
                        <div class="div-header font-normal mb-1 hover:bg-transparent hover:text-emon-accent">
                            logout
                        </div>
                    </a>
                <?php } else { ?>
                    <a href="index.php">
                        <div class="div-header font-normal mb-1 hover:bg-transparent hover:text-emon-accent">
                            Home
                        </div>
                    </a>

                <?php } ?>
            </div>

            <!-- navbar end -->