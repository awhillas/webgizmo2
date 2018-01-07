<!DOCTYPE html>
<html lang="<?=$language?>">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Content meta tags -->
    <title><?=$this->e($title)?></title>

    <?php $this->insert('partials/css') ?>
  </head>
  <body>
    <?php $this->insert('partials/header') ?>

    <?=$this->section('content')?>

    <?php $this->insert('partials/footer') ?>
  </body>
</html>
