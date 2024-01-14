<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, usePage} from '@inertiajs/vue3';
import '/resources/css/friends/index.css';
import { Inertia } from '@inertiajs/inertia';

const props = usePage().props;
const friendsList = ref(props.friendsList);

const addFriend = (friend) => {
    Inertia.post('/friends/add-friend-request', { friendId: friend.id });
    friend.requestSent = true;
};

const cancelRequest = (friend) => {
    Inertia.post('/friends/cancel-friend-request', { friendId: friend.id });
    friend.requestSent = false;
};
</script>

<template>
    <Head title="Dashboard"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ищи новых кентов!</h2>
        </template>
        <div class="friend-list" v-for="friend in friendsList" :key="friend.id">
            <div class="py-12" style="padding-bottom: 0">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="card p-6 text-gray-900">
                            <img class="preview-image" src="#">
                            <div>{{ friend.name }}</div>
                            <button
                                class="friend-button"
                                :class="{ 'request-sent': friend.requestSent }"
                                @click="friend.requestSent ? cancelRequest(friend) : addFriend(friend)"
                                @mouseover="friend.requestSent && (friend.hover = true)"
                                @mouseleave="friend.requestSent && (friend.hover = false)"
                            >
                                <template v-if="friend.requestSent">
                                    <span v-if="friend.hover">Отменить запрос</span>
                                    <span v-else> Запрос отправлен</span>
                                </template>
                                <template v-else>
                                    Добавить в друзья
                                </template>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
