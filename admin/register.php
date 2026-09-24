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
    <link href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1 class="title">Регистрация нового пользователя</h1>
        <form action="register.php" method="POST">
            <div class="field">
                <label class="label">Имя пользователя</label>
                <div class="control">
                    <input class="input" type="text" name="username" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Пароль</label>
                <div class="control">
                    <input class="input" type="password" name="password" required>
                </div>
            </div>
            <div class="control">
                <button class="button is-primary" type="submit">Зарегистрировать</button>
            </div>
        </form>
    </div>
</body>

</html>