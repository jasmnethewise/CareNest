const API_KEY = "AIzaSyC3UWFLKpHWivzfT-AomK7UVF7xyy16P4k";

const chatBox = document.getElementById("chatBox");
const userInput = document.getElementById("userInput");
const sendBtn = document.getElementById("sendBtn");
const micBtn = document.getElementById("micBtn");
const stopBtn = document.getElementById("stopBtn");

let isSpeaking = false;
let synth = window.speechSynthesis;
let recognition;


function formatText(text) {
  return text
    .replace(/\*\*(.*?)\*\*/g, "<b>$1</b>")
    .replace(/\*(.*?)\*/g, "<i>$1</i>")
    .replace(/^- (.*?)(?=<br>|$)/g, "• $1")
    .replace(/\n/g, "<br>");
}


function addMessage(sender, text) {
  const msg = document.createElement("div");
  msg.className = `message ${sender}`;
  msg.innerHTML = formatText(text);
  chatBox.appendChild(msg);
  chatBox.scrollTop = chatBox.scrollHeight;
}


function speak(text) {
  if (!synth) return;
  stopSpeech();
  const utter = new SpeechSynthesisUtterance(text.replace(/<[^>]*>/g, ""));
  utter.lang = "en-US";
  utter.rate = 1.0;
  utter.onstart = () => (isSpeaking = true);
  utter.onend = () => (isSpeaking = false);
  synth.speak(utter);
}


function stopSpeech() {
  if (synth.speaking) synth.cancel();
  isSpeaking = false;
}


async function fetchGemini(prompt) {
  const body = {
    contents: [
      {
        parts: [
          {
            text: `You are a helpful health assistant. Always answer responsibly, clearly, and professionally. Question: ${prompt}`,
          },
        ],
      },
    ],
  };

  try {
    const res = await fetch(
      `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=${API_KEY}`,
      {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(body),
      }
    );

    const data = await res.json();
    return (
      data?.candidates?.[0]?.content?.parts?.[0]?.text ||
      "Sorry, I couldn’t generate a response."
    );
  } catch (err) {
    console.error("Gemini API error:", err);
    return "⚠️ Error connecting to the AI model.";
  }
}


async function checkHealthRelated(prompt) {
  const body = {
    contents: [
      {
        parts: [
          {
            text: `Decide if the following question is related to healthcare, medicine, wellness, nutrition, or fitness. 
Respond only with "YES" or "NO".
Question: ${prompt}`,
          },
        ],
      },
    ],
  };

  try {
    const res = await fetch(
      `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=${API_KEY}`,
      {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(body),
      }
    );

    const data = await res.json();
    const answer =
      data?.candidates?.[0]?.content?.parts?.[0]?.text?.trim().toUpperCase() ||
      "NO";
    return answer === "YES";
  } catch (err) {
    console.error("Health check error:", err);
    return false;
  }
}


async function sendMessage() {
  const text = userInput.value.trim();
  if (!text) return;

  addMessage("user", text);
  userInput.value = "";

  addMessage("bot", "⏳ Checking...");
  const botThinking = chatBox.lastChild;


  const isHealthRelated = await checkHealthRelated(text);

  if (!isHealthRelated) {
    botThinking.remove();
    addMessage(
      "bot",
      "⚕️ Sorry, I can only answer questions related to health, medicine, or wellness."
    );
    return;
  }


  botThinking.innerHTML = "⏳ Thinking...";
  const response = await fetchGemini(text);
  botThinking.remove();

  addMessage("bot", response);
  speak(response);
}



function initSpeechRecognition() {
  const SpeechRecognition =
    window.SpeechRecognition || window.webkitSpeechRecognition;
  if (!SpeechRecognition) {
    alert("Speech recognition not supported in this browser.");
    return;
  }

  recognition = new SpeechRecognition();
  recognition.lang = "en-US";
  recognition.interimResults = false;
  recognition.continuous = false;

  recognition.onstart = () => micBtn.classList.add("listening");
  recognition.onend = () => micBtn.classList.remove("listening");
  recognition.onresult = (e) => {
    const text = e.results[0][0].transcript;
    userInput.value = text;
    sendMessage();
  };
}

stopBtn.addEventListener("click", stopSpeech);
sendBtn.addEventListener("click", sendMessage);
userInput.addEventListener("keypress", (e) => {
  if (e.key === "Enter") sendMessage();
});
