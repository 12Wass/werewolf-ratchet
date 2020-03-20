
// WebSocket
var conn = new WebSocket('ws://localhost:8080'); 

let name = window.prompt("Quel est votre pseudo?"); 
let nameSetter = {message: name, sender: "Wassim", command: "setName"};
document.getElementById("myName").innerHTML = name;

conn.onopen = () =>  {
    conn.send(JSON.stringify(nameSetter));
    getNames = {command: "getNames"}; 
    conn.send(JSON.stringify(getNames)); 
};


if (name !== null){
    // Généralités
    let msgerchat = document.getElementById("msger-chat"); 

    // Réception de messages
    conn.onmessage = function(e) {
        let received = JSON.parse(e.data);
        console.log(e); 
        if (received.command == "genMessage"){
          let newMessage = generateMessage("received", received.message, received.sender);
          msgerchat.insertAdjacentHTML("beforeend", newMessage); 
          msgerchat.scrollTop += 500;
        }
        if (received.command == "setName"){
          let usersList = document.getElementById("usersList"); 
          usersList.innerHTML +=  "<div id='" + received.message + "'>" + received.message + "</div>"; 
        }
        if (received.command == "getNames"){
          addNameToConversations(received); 
        }
        if (received.command == "disconnectUser"){
          disconnectUser(received.user);  
        }
    };
    

    // Envoi de message
    var input = document.getElementById("messageToSend");
    input.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            let data = {message: input.value, sender: name, command: "genMessage"};
            conn.send(JSON.stringify(data));
           
            let msgHTML = generateMessage("sent", input.value, name); 
            msgerchat.insertAdjacentHTML("beforeend", msgHTML); 
            msgerchat.scollTop += 500; 
            input.value = "";
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
    usersList.innerHTML += "<div id='" + received[key] + "'>" + received[key] + "</div>"; 
  }
}
function disconnectUser(user){
  document.getElementById(user).remove(); 
}