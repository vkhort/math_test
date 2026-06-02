<?php
// Отключаем кэширование, чтобы изменения применялись мгновенно при тестах
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Имитируем чтение данных из вашей будущей Базы Данных
$formula_from_db = '<p>Исходные данные из БД. Нажмите кнопку <span style="color:orange">√</span> для создания многоуровневой формулы:</p>';

// Перехватываем сохранение формы (POST запрос)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editor_content = $_POST['answerEditor'] ?? '';
    $formula_from_db = $editor_content;
    // Здесь будет ваш будущий код записи в БД (PDO / mysqli)
}

/**
 * Функция-прокси: PHP скачивает код скрипта с оригинального CDN сервера.
 * Так как Vercel находится вне блокировок, он скачает файлы за миллисекунды.
 */
function fetchExternalScript($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Разрешаем редиректы (важно для tiny.cloud)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

// Сервер Vercel скачивает скрипты в свою память
$tinymce_core_code = fetchExternalScript('https://cloudflare.com');
$mathtype_plugin_code = fetchExternalScript('https://unpkg.com');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>PHP + Vercel Серверный Прокси Редактора</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f6f9; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .btn-submit { margin-top: 15px; padding: 10px 20px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
    </style>

    <!-- ВСТРАИВАЕМ КОД ТЕКСТОМ: Ваш браузер получит этот код напрямую с домена Vercel! -->
    <script>
        <?php echo $tinymce_core_code; ?>
    </script>

    <!-- Скрипт-визуализатор от Wiris -->
    <script src="https://wiris.net"></script>
</head>
<body>

<div class="container">
    <h2>Редактирование поля базы данных (Vercel Serverless PHP)</h2>
    
    <form method="POST" action="">
        <textarea id="answerEditor" name="answerEditor">
            <?php echo $formula_from_db; ?>
        </textarea>
        <button type="submit" class="btn-submit">Сохранить изменения в базу</button>
    </form>
</div>

<script>
    // Регистрируем локально подгруженный код плагина MathType в глобальной памяти TinyMCE.
    // Это полностью избавляет нас от ошибок Cross-Origin (CORS) и блокировок external_plugins!
    tinymce.PluginManager.add('tiny_mce_wiris', function(editor, url) {
        <?php echo $mathtype_plugin_code; ?>
    });

    // Запуск редактора
    tinymce.init({
        selector: '#answerEditor',
        height: 280,
        menubar: false,
        plugins: ['lists', 'advlist', 'wordcount'], 
        
        // Так как код плагина мы зарегистрировали строкой выше через PluginManager.add,
        // нам больше НЕ НУЖЕН параметр external_plugins! Просто активируем его:
        forced_plugins: ['tiny_mce_wiris'],
        
        // Выводим оригинальную оранжевую кнопку MathType
        toolbar: 'undo redo | bold italic | bullist numlist | tiny_mce_wiris_formulaEditor',
        
        draggable_modal: true, 
        branding: false,
        statusbar: true,
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
            });
        }
    });
</script>

</body>
</html>


