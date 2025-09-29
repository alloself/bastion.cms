<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Bastion.CMS</title>
    <?php echo app('Illuminate\Foundation\Vite')('resources/ts/admin/index.ts'); ?>
</head>

<body>
    <?php echo csrf_field(); ?>
    <div id="admin"></div>
</body>

</html>
<?php /**PATH /var/www/resources/views/admin.blade.php ENDPATH**/ ?>