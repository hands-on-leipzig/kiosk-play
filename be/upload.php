<?php
session_start();

$correctPassword = "geschlechtspartner";
$uploadDir = "public/photos/";

// Handle password submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === $correctPassword) {
        $_SESSION['authenticated'] = true;
    } else {
        $error = "Incorrect password!";
    }
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $targetFile = $uploadDir . basename($_FILES['image']['name']);
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        $success = "Image uploaded successfully!";
    } else {
        $error = "Failed to upload image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Photo Upload</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        h2 {
            margin-bottom: 15px;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        input[type="password"], input[type="file"] {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        button {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }

        .success {
            color: green;
            margin-bottom: 10px;
        }

        .gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }

        .photo {
            width: 100px;
            height: 100px;
            overflow: hidden;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

    </style>
</head>
<body>

<div class="container">
    <?php if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']): ?>
        <h2>Enter Password</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    <?php else: ?>
        <h2>Upload Photos</h2>
        <?php if (isset($success)): ?>
            <p class="success"><?= $success ?></p>
        <?php elseif (isset($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="image" accept="image/*" required>
            <button type="submit">Upload</button>
        </form>

        <h3>Uploaded Photos</h3>
        <div class="gallery">
            <?php
            $files = glob($uploadDir . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);
            foreach ($files as $file):
                ?>
                <div class="photo">
                    <img src="<?= $file ?>" alt="Uploaded Photo">
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
