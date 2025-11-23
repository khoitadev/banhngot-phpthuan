(function () {
    const launcher = document.getElementById('chatbot-launcher');
    const panel = document.getElementById('chatbot-panel');
    const form = document.getElementById('chatbot-form');
    const input = document.getElementById('chatbot-input');
    const messages = document.getElementById('chatbot-messages');

    if (!launcher || !panel || !form || !input || !messages) {
        return;
    }

    const appendMessage = (text, sender) => {
        const bubble = document.createElement('div');
        bubble.className = `chatbot-message ${sender}`;
        bubble.textContent = text;
        messages.appendChild(bubble);
        messages.scrollTop = messages.scrollHeight;
        return bubble;
    };

    const updateMessage = (bubble, text) => {
        if (bubble) {
            bubble.textContent = text;
        }
    };

    const togglePanel = () => {
        panel.classList.toggle('open');
        if (panel.classList.contains('open')) {
            input.focus();
        }
    };

    launcher.addEventListener('click', togglePanel);

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const text = input.value.trim();

        if (!text) {
            return;
        }

        appendMessage(text, 'user');
        input.value = '';

        const thinkingBubble = appendMessage('Đang soạn câu trả lời...', 'bot');

        try {
            const response = await fetch('chatbot.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ message: text })
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            updateMessage(thinkingBubble, data.reply || 'Xin lỗi, tôi hiện không có câu trả lời cho câu hỏi đó.');
        } catch (error) {
            console.error(error);
            updateMessage(thinkingBubble, 'Có lỗi xảy ra. Vui lòng thử lại sau.');
        }
    });
})();

