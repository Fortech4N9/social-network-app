<script>
import {onMounted, reactive, toRefs} from "vue";
import useChat from "@/composables/chat.js";

export default {
    props: {
        isOpen: Boolean,
        friend: {
            required: true,
            type: Object
        },
        user: {
            required: true,
            type: Object
        },
    },
    emits: ['close'],
    methods: {
        closeChat() {
            this.$emit('close');
        },
        formatDate(dateString) {
            const options = {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            };
            const date = new Date(dateString);
            const locale = 'ru-RU';

            const formattedDate = new Intl.DateTimeFormat(locale, options).format(date).replace(',', '');
            const [datePart, timePart] = formattedDate.split(' ');

            return `${timePart} ${datePart}`;
        }
    },
    setup(props) {
        const form = reactive({
            message: '',
        })

        const {
            messages,
            errors,
            getMessages,
            addMessage
        } = useChat();

        const sendMessage = async (chatId) => {
            await addMessage(form, chatId);

            form.message = '';
        };
        if (props.friend !== null) {
            onMounted(() => {
                getMessages(props.friend.id);
            });
        }
        Echo.private(`chat`)
            .listen('MessageSent', (e) => {
                messages.value.push(e.message);
            })
        return {
            errors,
            form,
            sendMessage,
            messages
        };
    },
};
</script>

<template>
    <div class="chat-modal" v-if="isOpen">
        <div class="chat-header">
            <h3 class="tittle-modal">Чат с {{ friend.name }}</h3>
            <button class="close-modal" @click="closeChat">Закрыть</button>
        </div>
        <div class="chat-body">
            <div class="messages-history">
                <div class="message" v-for="message in messages">
                    <span v-if="message.sender_id !=user.id" class="username">{{ friend.name }}: </span>
                    <span v-else class="username">Вы: </span>
                    <span class="text">{{ message.message }}</span>
                    <span class="timestamp">{{ formatDate(message.updated_at) }}</span>
                </div>
            </div>
            <form class="send-message-form">
                <div v-if="errors" class="test-red-500 py-4">
                    <div v-for="(message,key) in errors.message" :key="key">
                        {{ message }}
                    </div>
                </div>
                <input v-model="form.message" @keyup.enter="sendMessage(friend.chatId)" type="text"
                       placeholder="Введите ваше сообщение здесь..."/>
                <button @click="sendMessage(friend.chatId)" type="button">Отправить</button>
            </form>
        </div>
    </div>
</template>
