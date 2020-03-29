<!DOCTYPE html>
<html>
<head>
    <title>Ratchet</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/msger-style.css">
    <link rel="stylesheet" href="css/uikit.min.css">
</head>

<body>
<aside id="usersList">
  <p onclick="backToGeneral()">Retour au chat général</p>
  <h4>Utilisateurs connectés : </h4>
</aside>
<section class="msger">
  <header class="msger-header">
    <div class="msger-header-title">
      <i class="fas fa-comment-alt"></i> Partie : Loup-garou
      <p class="uk-text-right" id="myName"></p>
    </div>
    <div class="msger-header-options">
      <span><i class="fas fa-cog"></i></span>
    </div>
  </header>

  <main class="msger-chat" id="msger-chat">
  </main>

  <div class="msger-inputarea">
    <input type="text" class="msger-input" placeholder="Entrez votre message..." id="messageToSend">
    <button type="submit" class="msger-send-btn">Envoyer</button>
  </div>
</section>
<script src="js/uikit.min.js"></script>
<script src="js/chat.js"></script>
</body>
</html>