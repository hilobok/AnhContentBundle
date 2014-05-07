<?php if (!$isImage) { ?>
    <a class="asset" href="<?php echo $src; ?>"><?php echo $title ?: basename($src); ?></a>
<?php } elseif ($align == 'none') { ?>
    <img src="<?php echo $src; ?>" alt="<?php echo $alt; ?>" />
<?php } else { ?>
    <figure class="image align-<?php echo $align; ?>">
        <img src="<?php echo $src; ?>" alt="<?php echo $alt; ?>" />

        <?php if (!empty($title)) { ?>
            <figcaption><?php echo $title; ?></figcaption>
        <?php } ?>
    </figure>
<?php } ?>
