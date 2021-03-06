<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $page_title ?></title>
    <!-- <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet"> -->
    <!-- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="<?= base_url("css/nunito.css"); ?>">
    <link rel="stylesheet" href="<?= base_url("css/all.min.css"); ?>">
    <link rel="stylesheet" href="<?= base_url("css/bootstrap.min.css"); ?>">
    <link rel="stylesheet" href="<?= base_url("css/bootstrap-select.css") ?>" />
    <link rel="stylesheet" href="<?= base_url("css/bootstrap-datepicker3.standalone.min.css") ?>" />
    <link rel="stylesheet" href="<?= base_url("css/custom.css"); ?>">
    <link rel="shortcut icon" href="<?= base_url("favicon.ico"); ?>" type="image/x-icon">
    <link rel="icon" href="<?= base_url("favicon.ico"); ?>" type="image/x-icon">
    <script src="<?= base_url("js/jquery.min.js") ?>"></script>
    <script>
        var baseUrl = "<?= base_url() ?>";
    </script>
</head>

<body>
    <div class="loader-wrapper">
        <div class="loader"></div>
        <div class="loader slow"></div>
    </div>
    <div id="wrapper">