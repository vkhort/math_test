<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Имитируем чтение данных из вашей будущей Базы Данных на Vercel
$formula_from_db = '<p>Исходный текст из БД. Нажмите чёрную кнопку <span style="color:black; font-weight:bold;">√</span> для ввода многоуровневой формулы:</p>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editor_content = $_POST['answerEditor'] ?? '';
    $formula_from_db = $editor_content;
}

// Сервер Vercel мгновенно читает код ядра TinyMCE прямо из вашего корневого файла tinymce_code.js
$tinymce_core_code = @file_get_contents(__DIR__ . '/tinymce_code.js');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Автономный редактор MathType на Vercel PHP</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f6f9; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .btn-submit { margin-top: 15px; padding: 10px 20px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
    </style>

    <!-- Встраиваем код ядра TinyMCE, прочитанный сервером из файла tinymce_code.js -->
    <script>
        <?php echo $tinymce_core_code; ?>
    </script>
    
    <!-- Скрипт-визуализатор от Wiris -->
    <script src="https://wiris.net"></script>
</head>
<body>

<div class="container">
    <h2>Редактирование поля базы данных (Vercel PHP Финал)</h2>
    
    <form method="POST" action="">
        <textarea id="answerEditor" name="answerEditor">
            <?php echo $formula_from_db; ?>
        </textarea>
        <button type="submit" class="btn-submit">Сохранить изменения в базу</button>
    </form>
</div>

<script>
    // Запуск редактора TinyMCE
    tinymce.init({
        selector: '#answerEditor',
        
        // Указываем путь к локальной папке со стилями внутри сервера Vercel
        base_url: 'tinymce', 
        suffix: '.min', 
        
        height: 300,
        menubar: false,
        branding: false,
        statusbar: true,
        
        // Редактор сам найдет плагины в вашей загруженной папке tinymce/plugins/
        plugins: ['lists', 'advlist', 'wordcount', 'tiny_mce_wiris'], 
        toolbar: 'undo redo | bold italic | bullist numlist | tiny_mce_wiris_formulaEditor',
        
        draggable_modal: true,
        setup: function(editor) {
            editor.on('change', function() {
                editor.save(); 
            });
        }
    });
</script>

</body>
</html>
