<!DOCTYPE html>
<html lang="<?=$language?>">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Content meta tags -->
    <title><?=$this->e($title)?></title>

    <?php $this->insert('partials/stylesheets') ?>
  </head>
  <body>
    <header>
      <?php $this->insert('partials/header') ?>
    </header>

    <main>
      <?=$this->section('content')?>
    </main>

    <footer>
      <?php $this->insert('partials/footer') ?>
    </footer>

    <?php $this->insert('partials/javascript') ?>
  </body>
</html>
