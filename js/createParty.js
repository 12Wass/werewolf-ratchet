function cardsManager(){
    let cardsDiv = document.getElementById("cardsList"); 
    let request = new XMLHttpRequest();
    let selectedCards = new Array;  
    request.onreadystatechange = function() {
        if (request.readyState === 4 && request.status === 200) {
            let cards = JSON.parse(request.response);
            for (let i in cards){
               cardsDiv.innerHTML += 
               "<div class='uk-card uk-card-default uk-card-body' id=" + cards[i]['id'] + "><div class='uk-card-badge uk-label card' id=" + cards[i]['name'] +">Ajouter</div><h3 class='uk-card-title'>"+ cards[i]['name'] + "</h3><p>" + cards[i]['description'] + "</p></div>";
            }

            let cardsSelector = document.querySelectorAll('.card'); // Sélectionne toutes les cartes créées juste avant
            cardsSelector.forEach((btn) => { // Boucle sur chaque carte créée
                btn.addEventListener("click", (event) => { // Mets en place un écouteur d'événement
                  if (selectedCards.hasOwnProperty(event.target.id)){ // Si l'array existe déjà
                       selectedCards[event.target.id] += 1;
                       console.log(selectedCards);
                  } else if (event.target.id == ""){ // Si le clic n'ajoute rien 
                        console.error("TargetError: Couldn't get clicked element in time");
                  } else { // Si l'array est vide 
                        selectedCards[event.target.id] = 1;
                        console.log(selectedCards);
                    }
                })  
            })
        }
    };
    request.open('GET', '../bin/getCards.php');
    request.setRequestHeader('Content-Type', 'application/json');
    request.send();
}