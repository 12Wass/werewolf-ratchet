
// WebSocket
var conn = new WebSocket('ws://localhost:8080'); 
conn.onopen = function(e) {
    console.log("Connection established!");
};

let name = window.prompt("Quel est votre pseudo?"); 
conn.name = name; 
if (name !== null){
    // Généralités
    let msgerchat = document.getElementById("msger-chat"); 

    // Réception de messages
    conn.onmessage = function(e) {
        let receivedMessage = JSON.parse(e.data); 
        let newMessage = generateMessage("received", receivedMessage.message, receivedMessage.sender); // Remplacer "test" par le pseudo reçu. 
        msgerchat.insertAdjacentHTML("beforeend", newMessage); 
        msgerchat.scrollTop += 500; 
    };

    // Envoi de message
    var input = document.getElementById("messageToSend");
    input.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            let data = {message: input.value, sender: name};
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
