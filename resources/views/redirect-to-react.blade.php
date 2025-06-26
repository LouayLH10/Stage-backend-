<!DOCTYPE html>
<html>
<head>
    <title>Redirection en cours...</title>
</head>
<body>
<script>
  // Envoyer les données à React via postMessage
  window.opener?.postMessage({
    name: @json($name),
    email: @json($email),
    token: @json($token),
  }, "http://localhost:3000");

  // Fermer la fenêtre si elle est popup
  window.close();

  // Sinon, rediriger
  window.location.href = "http://localhost:3000";
</script>
</body>
</html>
  