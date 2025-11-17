<?php global $base_root, $base_path; ?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="Generator" content="" />
        <?php print $head; ?>
        <title>:: Welcome to e-Allotment of Rental Housing Estate | e-Allotment of Rental Housing Estate ::</title>
        <?php print $styles; ?>
        <link rel="stylesheet" href="<?= $base_root.$base_path ?>sites/all/themes/housingtheme/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?= $base_root.$base_path ?>sites/all/themes/housingtheme/css/bootstrap-icons.min.css">
        <link rel="stylesheet" href="<?= $base_root.$base_path ?>sites/all/themes/housingtheme/css/style.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Freeman&display=swap">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="<?= $base_root.$base_path ?>sites/all/themes/housingtheme/css/font-awesome.css">
        

        <!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->

        <?php print $scripts; ?>
    </head>
    <body class="min-vh-100">
        <?php // print $page_top; ?>
        <?php print $page; ?>
        <?php // print $page_bottom; ?>
        <script src="<?= $base_root.$base_path ?>sites/all/themes/housingtheme/js/bootstrap.bundle.min.js"></script>
        <script src="<?= $base_root.$base_path ?>sites/all/themes/housingtheme/js/custome.js"></script>
    </body>
</html>
