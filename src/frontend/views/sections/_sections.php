<?php

/**
 * @var \bulldozer\pages\frontend\ar\Section[] $sections
 */

?>
<?php if (count($sections)): ?>
    <div class="row">
        <?php foreach ($sections as $section): ?>
            <div class="col-md-3">
                <a href="<?=$section->viewUrl?>">
                    <?php if ($section->image): ?>
                        <img src="<?= $section->image->getThumbnail(320, 240) ?>" style="width: 320px;">
                    <?php endif ?>

                    <div class="title">
                        <?=$section->name?>
                        <?php if ($section->active == 0): ?>
                            (Раздел неактивен)
                        <?php endif ?>
                    </div>
                </a>
            </div>
        <?php endforeach ?>
    </div>
<?php endif ?>