<!DOCTYPE html>
<html>
<head>
    <title>Profil Google</title>
</head>
<body>
    <h1>Bienvenue, {{ $name }}</h1>
    <p>Email : {{ $email }}</p>
    <p>Token API généré :</p>
    <textarea rows="4" cols="100">{{ $token }}</textarea>

    <br><br>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Se déconnecter</button>
    </form>
</body>
</html>
