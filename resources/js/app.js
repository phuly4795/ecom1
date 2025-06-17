import "./bootstrap";

import Alpine from "alpinejs";

window.Alpine = Alpine;
// Ví dụ sử dụng realtime
window.Echo.channel("contact-messages").listen("NewContactMessage", (e) => {
    alert("Tin nhắn mới từ: " + e.contact.name);
});
Alpine.start();
