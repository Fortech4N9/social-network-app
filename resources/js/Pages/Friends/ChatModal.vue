<script>
import {onMounted, reactive} from "vue";
import useChat from "@/composables/chat.js";

export default {
    props: {
        isOpen: Boolean,
        friend: Object,
        user: {
            required: true,
            type: Object
        },
    },
    methods: {
        closeChat() {
            this.$emit('close');
        }
    },
    setup() {
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

        onMounted(getMessages)

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
        {{ friend }}
        <div class="chat-header">
            <h3 class="tittle-modal">Чат с {{ friend.name }}</h3>
            <button class="close-modal" @click="closeChat">Закрыть</button>
        </div>
        <div class="chat-body">
            <div class="messages-history">
                <div class="message" v-for="message in messages">
                    <span v-if="message.user.id !=user.id" class="username">{{friend.name}}</span>
                    <span v-else class="username">Вы</span>
                    <span class="text">{{message.message}}</span>
                    <span class="timestamp">{{message.updated_at}}</span>
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
                <button @click="sendMessage(friend.chatId)" type="submit">Отправить</button>
            </form>
        </div>
    </div>
</template>
