<!DOCTYPE html>
<html>
<head>
    <title>Подтверждение электронной почты</title>
</head>
<body>
    <h1>Здравствуйте, {{ $user->name }}!</h1>
    <p>Пожалуйста, подтвердите вашу электронную почту, перейдя по следующей ссылке:</p>
    <a href="{{ url('api/verify/' . $user->verification_token) }}">Подтвердить электронную почту</a>
    <p>Спасибо!</p>
</body>
</html>
