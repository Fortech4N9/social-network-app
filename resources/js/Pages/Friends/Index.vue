<script setup>
import {onMounted, ref} from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import '/resources/css/friends/index.css';
import { Inertia } from '@inertiajs/inertia';
// Импортируйте ChatModal если он находится в отдельном файле
import ChatModal from './ChatModal.vue';

const props = usePage().props;
const friendsList = ref(props.friendsList.map(friend => ({
    ...friend,
    hidden: false
})));

const currentChatFriend = ref(null);
const isChatOpen = ref(false);
onMounted(() => {
    const visibleFriend = friendsList.value.find(friend => !friend.hidden);
    if (visibleFriend) {
        currentChatFriend.value = visibleFriend;
    }
});

const openChat = (friend) => {
    currentChatFriend.value = friend;
    isChatOpen.value = true;
};

const closeChat = () => {
    isChatOpen.value = false;
};

const declineFriendship = (friend) => {
    Inertia.post('/friends/cancel-friend', { friendId: friend.id });
    friend.hidden = true;
};
</script>

<template>
    <Head title="Dashboard"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Твои кенты</h2>
        </template>
        <div v-for="friend in friendsList" :key="friend.id" v-show="!friend.hidden">
            <div class="py-12" style="padding-bottom: 0">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="card p-6 text-gray-900">
                            <img class="preview-image" src="#">
                            <div>{{ friend.name }}</div>
                            <button class="message-button" @click="openChat(friend)">
                                Открыть чат
                            </button>
                            <button class="friend-button" @click="declineFriendship(friend)">
                                Удалить из друзей
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <ChatModal
            v-if="isChatOpen"
            :key="currentChatFriend.value ? currentChatFriend.value.id : ''"
            :is-open="isChatOpen"
            :friend="currentChatFriend"
            :user="props.user"
            @close="closeChat"
        />
    </AuthenticatedLayout>
</template>
