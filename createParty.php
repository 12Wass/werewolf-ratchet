<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Créer une partie</title>
        <script src="js/uikit.js"></script>
        <script src="js/uikit-icons.js"></script>
        <link rel="stylesheet" type="text/css" href="css/uikit.css">
        <style>
            a {
                text-decoration: none;
            }
        </style>
    </head>

    <body onload="cardsManager()">
        <h1 class="uk-text-center">Paramétrage de la partie</h1>
        <div class="uk-flex">
            <div id="cardsList" class="uk-width-1-2"></div>
        </div>
        
        <!-- 
        cardsDiv.innerHTML += "<div id=" + cards[i]['id'] + "><h3 class='cardName'>" + cards[i]['name'] + " <i class='card' uk-icon='plus' id=" + cards[i]['name'] + "></i></h3>" + "<p>" + cards[i]["description"] + "</p>"; 
        Ajouter la liste des cartes ajoutées -->


        
        <script src="js/createParty.js"></script>
    </body>
</html>