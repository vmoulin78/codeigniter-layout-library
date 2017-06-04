<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html> 
    <head>
        <?php $CI->layout->trigger_title(); ?>
        <?php $CI->layout->trigger_charset(); ?>
        <?php $CI->layout->trigger_metadata(); ?>
        <?php $CI->layout->trigger_http_equiv(); ?>
        <?php $CI->layout->trigger_css(); ?>
    </head>

    <body>
        <div>
            <header>
                <?php $CI->layout->block('menu_block'); ?>
            </header>

            <hr />

            <?php $CI->layout->trigger_breadcrumb(); ?>

            <hr />

            <section>
                <?php $CI->layout->trigger_content_section('main'); ?>
            </section>

            <hr />

            <?php $CI->layout->include_template('footer_partial'); ?>
        </div>

        <?php $CI->layout->trigger_js(); ?>

    </body>

</html>