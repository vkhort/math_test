<?php
// Отключаем кэширование, чтобы страница обновлялась мгновенно при тестах
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

$formula_from_db = '<p>Исходные данные из БД. Нажмите кнопку <span style="color:orange">√</span> для создания многоуровневой формулы:</p>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editor_content = $_POST['answerEditor'] ?? '';
    $formula_from_db = $editor_content;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Официальная облачная интеграция MathType</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f6f9; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .btn-submit { margin-top: 15px; padding: 10px 20px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
    </style>

    <!-- 1. ПОДКЛЮЧАЕМ TINYMCE 6: Используем официальный открытый сервер cdn.tiny.cloud -->
    <script src="https://tiny.cloud" referrerpolicy="origin" crossorigin="anonymous"></script>

    <!-- 2. ПОДКЛЮЧАЕМ ВИЗУАЛИЗАТОР ФОРМУЛ: Официальный стабильный адрес от Wiris -->
    <script src="https://wiris.net"></script>
</head>
<body>

<div class="container">
    <h2>Редактирование поля базы данных (Официальное Облако)</h2>
    
    <form method="POST" action="">
        <textarea id="answerEditor" name="answerEditor">
            <?php echo $formula_from_db; ?>
        </textarea>
        <button type="submit" class="btn-submit">Сохранить изменения в базу</button>
    </form>
</div>

<script>
    // Инициализация редактора
    tinymce.init({
        selector: '#answerEditor',
        height: 280,
        menubar: false,
        plugins: ['lists', 'advlist', 'wordcount'], 
        
        // 3. ОБЛАЧНЫЙ ПЛАГИН: Подключаем оригинальный скрипт Wiris, созданный для внешних интеграций
        external_plugins: {
            'tiny_mce_wiris': 'https://www.wiris.net/demo/plugins/tiny_mce/plugin.js'
        },
        
        // Системные имена кнопок для вызова оригинального окна MathType
        toolbar: 'undo redo | bold italic | bullist numlist | tiny_mce_wiris_formulaEditor tiny_mce_wiris_formulaEditorChemistry',
        
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
