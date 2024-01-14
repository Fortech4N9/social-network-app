<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import '/resources/css/posts/index.css';
import {Head, usePage} from '@inertiajs/vue3';
import {ref} from "vue";
function  formatDate(dateString) {
    const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', hour12: false };
    const date = new Date(dateString);
    const locale = 'ru-RU';

    const formattedDate = new Intl.DateTimeFormat(locale, options).format(date).replace(',', '');
    const [datePart, timePart] = formattedDate.split(' ');

    return `${timePart} ${datePart}`;
}


const props = usePage().props;
const postsList = ref(props.posts);
</script>

<template>
    <Head title="Dashboard"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Новости друзей</h2>
        </template>
        <div v-for="post in postsList" :key="post.id">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="post-header">
                            <div class="post-author">
                                <img src="#">
                                <h3>{{post.name}}</h3>
                            </div>
                            <h2>{{post.title}}</h2>
                            <h3>{{formatDate(post.updated_at)}}</h3>
                        </div>
                        <div class="post-body">
                            {{post.content}}
                        </div>
                        <div class="post-footer">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
