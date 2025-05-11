<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Галерея машинок, врум-врум</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="#">About Cars</a></li>
                <li><a href="#">News</a></li>
                <li><a href="#">Contacts</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>#cars</h1>
        <p>Explore a world of cars</p>
        <div class="gallery">
            <?php
            $dir = 'images/';
            $files = scandir($dir);
            if ($files !== false) {
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'jpg') {
                        echo "<img src='$dir$file' alt='Cat Image'>";
                    }
                }
            }
            ?>
        </div>
    </main>
    <footer>
        <p>USM © 2024</p>
    </footer>
</body>
</html>