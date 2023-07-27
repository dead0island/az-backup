<?php
    include 'config.php';
    include 'functions.php';
?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-patible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="<?php echo description ?>">
        <link rel="icon" href="<?php echo $YOURLS_SITE ?>/frontend/favicon.ico">

        <title><?php echo "短链接" ?></title>

        <link rel="stylesheet" href="<?php echo $YOURLS_SITE ?>/frontend/dist/styles.css">

        <?php if (defined('backgroundImage')) : ?>
            <style>
                body {
                    background: url(<?php echo backgroundImage ?>) no-repeat center center fixed !important; 
                    background-size: cover !important;
                }
            </style>
        <?php else : ?>
            <style>
                body {
                    background-color: <?php echo colour ?>;
                }
            </style>
        <?php endif; ?>

        <style>
            .btn-primary {
                background-color: <?php echo colour ?>;
                border-color: <?php echo colour ?>;
            }

            .btn-primary:hover,
            .btn-primary:focus,
            .btn-primary:active {
                background-color: <?php echo adjustBrightness(colour, -15) ?>;
                border-color: <?php echo adjustBrightness(colour, -15) ?>;
            }
        </style>
<style>
/*重置背景*/
body {
	background-color: #ffffff;
}
/*渐变背景CSS*/
 #canvas-basic {
    position: fixed;
    display: block;
    width: 100%;
    height: 100%;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: -999;
}
/*毛玻璃*/
.card {
	background-color: rgba(255,255,255,0.3)!important;
  backdrop-filter: saturate(180%) blur(20px);
  -webkot-backdrop-filter: saturate(180%) blur(20px);
}
#url {
background-color: rgba(255,255,255,0.3)
  backdrop-filter: saturate(180%) blur(20px);
  -webkot-backdrop-filter: saturate(180%) blur(20px);
}
/*底话居中*/
.fw-light {
	margin: 0 auto;
	font-size: 18px;
}
</style>
    </head>
