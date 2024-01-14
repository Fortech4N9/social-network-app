<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, usePage} from '@inertiajs/vue3';
import '/resources/css/posts/index.css';
import UserPostsRedactor from "@/Pages/Posts/UserPostsRedactor.vue";

const props = usePage().props;
</script>

<template>
    <Head title="Dashboard"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Моя страница</h2>
        </template>
        <UserPostsRedactor
            :posts="props.posts"
        />
    </AuthenticatedLayout>
</template>
