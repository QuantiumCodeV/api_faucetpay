<!DOCTYPE html>
<html>
<head>
    <title>Подтверждение электронной почты</title>
</head>
<body>
    <h1>Здравствуйте, {{ $user->name }}!</h1>
    <p>Пожалуйста, подтвердите вашу электронную почту, перейдя по следующей ссылке:</p>
    <a href="{{ env('FRONT_URL') . '/account/confirm-email/' . $user->verification_token }}">Подтвердить электронную почту</a>
    <p>Спасибо!</p>
</body>
</html>
