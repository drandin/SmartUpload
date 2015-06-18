<?php
if ($filesCollection instanceof \SmartUpload\FileCollection):?>
    <h1>Images (<?=$filesCollection->count();?>)</h1><hr>
    <?php

    $i = $j = 0;

    $scriptOutput = 'fileOutput.php';

    foreach ($filesCollection as $file):

        /**
         * @var $file \SmartUpload\File
         */

        $j++;

        ?>
        <?php if (++$i == 1): ?><div class="row"><?php endif; ?>
        <div class="col-xs-2">
            <a href="/<?=$scriptOutput;?>?idFile=<?=$file->getIdFile()?>&disposition=inline" target="_blank"><img src="<?=$file->getPathToFileThumbnail();?>" class="img-thumbnail thumbnailMin"></a>
            <div class="thumbnailImg thumbnailMin">
                <small><a href="/<?=$scriptOutput;?>?idFile=<?=$file->getIdFile()?>" target="_blank">Скачать</a>&nbsp;&nbsp;&nbsp;<a href="#" class="deleteImg" id="<?=$file->getIdFile()?>">Удалить</a></small>
            </div>
        </div>
        <?php  if ($i == 6 || $j === $filesCollection->count()): $i = 0; ?></div><?php endif; ?>
    <?php endforeach; ?>
    <?php if ($j == 0): ?><p>There are not any images! We can upload file.</p><?php endif;
endif;