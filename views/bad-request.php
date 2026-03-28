<!DOCTYPE html>
<html lang="<?= App::getLocale() ?>">
<head>
    <title><?= e(trans('wobqqq.fortify::lang.errors.bad_request_title')) ?></title>
    <link href="<?= Url::asset('/modules/system/assets/css/styles.css') ?>" rel="stylesheet" />
    <script src="<?= Url::asset('modules/system/assets/js/framework-bundle.min.js') ?>"></script>
    <meta name="turbo-visit-control" content="disable" />
    <meta charset="utf-8" />
</head>
<body>
    <div class="container">
        <h1><i class="icon-exclamation-circle warning"></i> <?=  e(trans('wobqqq.fortify::lang.errors.bad_request_title')) ?></h1>
        <p class="lead"><?= e(trans('wobqqq.fortify::lang.errors.bad_request_message')) ?></p>
    </div>
</body>
</html>
