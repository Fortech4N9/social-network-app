import {ref} from "vue";
import axios from "axios";

export default function useChat() {
    const messages = ref([]);
    const errors = ref([]);

    const getMessages = async (userId) => {
        try {
            const response = await axios.get(`/friends/messages/${userId}`);
            messages.value = response.data;
        } catch (e) {
            console.error('Ошибка при получении сообщений:', e);
        }
    }

    const addMessage = async (form, chatId) => {
        errors.value = [];
        try {
            const response = await axios.post('/friends/send', {
                chatId: chatId,
                message: form.message // Только текст сообщения
            });
        } catch (e) {
            if (e.response.status === 422) {
                errors.value = e.response.data.errors;
            }
            console.error('Ошибка при отправке сообщения:', e);
        }
    }

    return {
        messages,
        errors,
        getMessages,
        addMessage
    };
}
