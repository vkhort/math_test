<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

$formula_from_db = '<p>Исходные данные из БД. Нажмите кнопку <span style="color:orange">√</span> для создания многоуровневой формулы:</p>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editor_content = $_POST['answerEditor'] ?? '';
    $formula_from_db = $editor_content;
}

function fetchExternalScript($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

// ВНИМАНИЕ: Сюда возвращены ПОЛНЫЕ пути к JS-файлам!
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

    <!-- Встраиваем код TinyMCE напрямую с вашего сервера Vercel -->
    <script>
        <?php echo $tinymce_core_code; ?>
    </script>

    <!-- Полный и правильный адрес скрипта-визуализатора от Wiris -->
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
    // Регистрируем локально подгруженный код плагина MathType
    tinymce.PluginManager.add('tiny_mce_wiris', function(editor, url) {
        <?php echo $mathtype_plugin_code; ?>
    });

    // Запуск редактора
    tinymce.init({
        selector: '#answerEditor',
        height: 280,
        menubar: false,
        
        // В TinyMCE встроенный локальный плагин нужно просто объявить здесь
        plugins: ['lists', 'advlist', 'wordcount', 'tiny_mce_wiris'], 
        
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
