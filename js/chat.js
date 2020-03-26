
// WebSocket
var conn = new WebSocket('ws://localhost:8080'); 

let name = window.prompt("Quel est votre pseudo?"); 
let nameSetter = {message: name, sender: "Wassim", command: "setName"};
let currentConversation = "general";
document.getElementById("myName").innerHTML = name;

conn.onopen = () =>  {
    conn.send(JSON.stringify(nameSetter));
    getNames = {command: "getNames"}; 
    conn.send(JSON.stringify(getNames)); 
};


if (name !== null && name !== ""){

    // Généralités
    let msgerchat = document.getElementById("msger-chat"); 

    // Réception de messages
    conn.onmessage = function(e) {
        let received = JSON.parse(e.data); 
        if (received.command == "genMessage"){
          if (currentConversation == "general"){
              let newMessage = generateMessage("received", received.message, received.sender);
              msgerchat.insertAdjacentHTML("beforeend", newMessage); 
              msgerchat.scrollTop += 500;
          } else {

          }
        }
        if (received.command == "setName"){
        //   if (received.message !== null){
          let usersList = document.getElementById("usersList"); 
          usersList.innerHTML += '<div id="' + received.message + '" onclick="createConversation(' + received.message + ')">' + received.message + '</div>';
        //  }
        }
        if (received.command == "getNames"){
          addNameToConversations(received); 
        }
        if (received.command == "disconnectUser"){
          disconnectUser(received.user);   
        }
        if (received.command == "nameUsed"){
          alert("Le pseudo choisit est déjà utilisé, veuillez réessayer."); 
          document.location.reload(true); // Recharger la page actuelle.
        }
        if (received.command == "privateMessage"){
          if (currentConversation == "general" || currentConversation !== received.sender){
              // Ici on notifie l'user qu'il a un message :
              UIkit.notification({
                message: received.sender + ' : "' + received.message + '"',
                status: 'primary',
                pos: 'top-center',
                timeout: 5000
            });
              let username = document.getElementById(received.sender); // On récupère tout l'objet username
              let uNotifications =  username.innerText.split(" ");  // On coupe l'innerText (pseudo + count si présent) en deux

              if (uNotifications.length === 1){ // Si jamais c'est le premier message reçu, on aura qu'un seul résultat dans le tableau 
                username.innerHTML = uNotifications[0] + " 1"; // Donc on mets en place le premier chiffre vu qu'il y en a aucun avant
              } else {
                let currentNotification = parseInt(uNotifications[1], 10) + 1; // parseInt convertit la deuxième partie du tableau en integer pour la traiter (+1)
                username.innerHTML = uNotifications[0] + " " + currentNotification; // Ici on fait la magie : La notification est bien présente! 

              }
          }
          else {
            if (currentConversation == received.sender){
              let htmlMessage = generateMessage("received", received.message, received.sender);
              msgerchat.insertAdjacentHTML("beforeend", htmlMessage); 
              msgerchat.scrollTop += 500;
            }
          }
        }
        if (received.command == "conversationCreated"){
          currentConversation = received.user; 

          let chatInner = document.getElementById("msger-chat"); 
          let userInner = document.getElementById(received.user);  

          userInner.style = "background-color: red"; 
          chatInner.innerHTML = ""; 
        }
        if (received.command == "conversationMessages"){
          let generateTest = generateMessage("sent", received.messages[0].message, received.messages[0].sender);
          msgerchat.insertAdjacentHTML("beforeend", generateTest); 
          // Pourquoi ça marche pas? Tout s'affiche mais tout s'efface directement après
          received.messages.forEach(element => {
            if (element.sender == name){
              let htmlMessage = generateMessage("sent", element.message, element.sender);
              msgerchat.insertAdjacentHTML("beforeend", htmlMessage); 
              msgerchat.scrollTop += 500; 
            } else {
              let htmlMessage = generateMessage("received", element.message, element.sender);
              msgerchat.insertAdjacentHTML("beforeend", htmlMessage); 
              msgerchat.scrollTop += 500;
            }
            console.log(element); 
          });
        }
    };
    

    // Envoi de message
    var input = document.getElementById("messageToSend");
    input.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
          if (currentConversation == "general"){
              let data = {message: input.value, sender: name, command: "genMessage"};
              conn.send(JSON.stringify(data));
            
              let msgHTML = generateMessage("sent", input.value, name); 
              msgerchat.insertAdjacentHTML("beforeend", msgHTML); 
              msgerchat.scrollTop += 500; 
              input.value = "";
          }
          else {
            // Gérer ici le cas d'une conversation privée entre deux personnes : 
              let data = {message: input.value, sender: name, command: "privateMessage", receiver: currentConversation}; 
              conn.send(JSON.stringify(data)); 
              let msgHTML = generateMessage("sent", input.value, name);
              msgerchat.insertAdjacentHTML("beforeend", msgHTML); 
              msgerchat.scrollTop += 500; 
              input.value = ""; 
          }
        }
    });
} else {
    alert("Vous devez sélectionner un pseudo. Veuillez recharger la page");
}

function generateMessage(type, message, name){
    if (type == "received"){
        return `<div class="msg left-msg">
      <div
       class="msg-img"
       style="background-image: url(https://image.flaticon.com/icons/svg/327/327779.svg)"
      ></div>

      <div class="msg-bubble">
        <div class="msg-info">
          <div class="msg-info-name">`+ name + `</div>
          <div class="msg-info-time">12:45</div>
        </div>

        <div class="msg-text">
          ` + message + `
        </div>
      </div>
    </div>`; 
    }
    else if (type == "sent"){
        return `<div class="msg right-msg">
      <div
       class="msg-img"
       style="background-image: url(https://image.flaticon.com/icons/svg/145/145867.svg)"
      ></div>

      <div class="msg-bubble">
        <div class="msg-info">
          <div class="msg-info-name">` + name +`</div>
          <div class="msg-info-time">12:46</div>
        </div>

        <div class="msg-text">
         ` + message + `
        </div>
      </div>
    </div>`; 
    }
}
function addNameToConversations(received){
  delete received.command; 
  let usersList = document.getElementById("usersList"); 
  for (let [key, value] in received){
    usersList.innerHTML +=  '<div id="' + received[key] + '" onclick="createConversation(' + received[key] + ')">' + received[key] + '</div>'; 
  }
}
function disconnectUser(user){
  if (user !== null && user !== ""){
  document.getElementById(user).remove(); 
  }
}
function createConversation(user){
  if (currentConversation !== "general" && currentConversation !== user.id){
    let currentUser = document.getElementById(currentConversation); 
    currentUser.style = "";
  }
  if (currentConversation !== user.id){
    let data = {message: user.id, sender: name, command: "createConversation"};
    conn.send(JSON.stringify(data));
  }
}