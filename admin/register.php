<?php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $passwordHash = password_hash($password, PASSWORD_DEFAULT); // Хэшируем пароль

    // Проверка на существующего пользователя
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        echo "Пользователь с таким именем уже существует!";
    } else {
        $query = "INSERT INTO users (username, password) VALUES ('$username', '$passwordHash')";
        if (mysqli_query($conn, $query)) {
            echo "Пользователь зарегистрирован успешно!";
        } else {
            echo "Ошибка: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1 class="h2">Регистрация нового пользователя</h1>
        <form action="register.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Имя пользователя</label>
                <input class="form-control" type="text" name="username" required>
                </div>
            <div class="mb-3">
                <label class="form-label">Пароль</label>
                <input class="form-control" type="password" name="password" required>
                </div>
            <button class="btn btn-primary" type="submit">Зарегистрировать</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>