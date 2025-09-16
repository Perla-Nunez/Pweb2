<!-- chat.php -->
<div id="chatContainer" class="fixed bottom-4 right-4 w-80 bg-white shadow-lg rounded-lg overflow-hidden transition-all duration-300">
    <!-- Encabezado del chat -->
    <div class="bg-blue-950 text-white p-2 flex justify-between items-center cursor-pointer" onclick="toggleChat()">
        <span class="font-bold">Chat</span>
        <button id="toggleChatBtn" class="text-white hover:text-gray-300">−</button>
    </div>
    
    <!-- Contenido del chat -->
    <div id="chatContent" class="h-64 overflow-y-auto p-2">
        <!-- Aquí se cargarán los mensajes -->
    </div>

    <!-- Formulario de envío de mensaje -->
    <form id="chatForm" class="flex p-2 border-t" onsubmit="sendMessage(event)">
        <input type="text" id="messageInput" class="flex-grow p-1 border rounded-l" placeholder="Escribe un mensaje...">
        <button type="submit" class="bg-blue-900 hover:bg-blue-700 text-white px-3 py-1 rounded-r">Enviar</button>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Verifica si el chat estaba minimizado en localStorage
    if (localStorage.getItem("chatMinimized") === "true") {
        minimizeChat();
    }
});

function toggleChat() {
    let chatContent = document.getElementById("chatContent");
    let chatForm = document.getElementById("chatForm");
    let toggleBtn = document.getElementById("toggleChatBtn");

    if (chatContent.style.display === "none") {
        // Expandir chat
        chatContent.style.display = "block";
        chatForm.style.display = "flex";
        toggleBtn.textContent = "−";
        localStorage.setItem("chatMinimized", "false");
    } else {
        // Minimizar chat
        minimizeChat();
    }
}

function minimizeChat() {
    document.getElementById("chatContent").style.display = "none";
    document.getElementById("chatForm").style.display = "none";
    document.getElementById("toggleChatBtn").textContent = "+";
    localStorage.setItem("chatMinimized", "true");
}

function sendMessage(event) {
    event.preventDefault();
    let message = document.getElementById("messageInput").value.trim();
    if (message === '') return;

    let chatBox = document.getElementById("chatContent");
    let messageElement = document.createElement('div');
    messageElement.classList.add('bg-gray-200', 'p-1', 'rounded', 'mb-1');
    messageElement.textContent = message;
    chatBox.appendChild(messageElement);

    document.getElementById("messageInput").value = ''; // Limpiar campo
    chatBox.scrollTop = chatBox.scrollHeight; // Desplazar hacia abajo
}
</script>
