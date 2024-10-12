<?php

if($_SERVER['REQUEST_METHOD'] === 'POST'){
        include_once 'tagServiceAutoLoad.php';

        $service = new \PageParser\ImgService($_POST['url'] ?? '');
        $collection = $service->getCollection();
    }
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<form action="" method="post">
    <input type="text" name="url" placeholder="Введите url страницы" value="<?=$_POST['url'] ?? null?>">
    <input type="submit" value="Go">
</form>

<?php if(!empty($collection)):?>
    <style>
        .grid-wrap{
            display: grid;
            grid-template-columns: repeat(4, 200px);
        }

        .grid-item img{
            width: 200px;
            height: 200px;
            object-fit: contain
        }
    </style>
    <br>
    <br>
    Всего изображений: <?=count($collection)?>
    Общий размер: <?=$service->getContentSize()?> Mb
    <div class="grid-wrap">
        <?php foreach ($collection as $item):?>
            <div class="grid-item">
                <img src="<?=$item['url']?>" alt="">
            </div>
        <?php endforeach;?>
    </div>
<?php endif;?>
</body>
</html>
