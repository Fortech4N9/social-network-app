<script setup>
import {ref} from "vue";
import '/resources/css/posts/index.css';
import PostModal from "@/Pages/Posts/PostModal.vue";
import {Inertia} from "@inertiajs/inertia";

const props = defineProps({
    posts: Object
});
const posts = ref(props.posts);
Echo.private(`post`)
    .listen('PostSent', (e) => {
        posts.value.push(e.post);
    })

const currentPost = ref(null);
const isPostModalOpen = ref(false);

const createPost = () => {
    isPostModalOpen.value = true;
    currentPost.value = ref(null);
};

const closePostModal = () => {
    isPostModalOpen.value = false;
};

const updatePost = (post) => {
    currentPost.value = post;
    isPostModalOpen.value = true;
    post.hidden = true;
};

const deletePost = (post) => {
    Inertia.post('/delete-post', {postId: post.id});
    post.hidden = true;
}
</script>

<template>
    <div v-for="post in posts" :key="post.id" class="mb-4" v-show="!post.hidden">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="post bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                    <div class="p-6 text-gray-900">

                        <h3 class="font-bold">{{ post.title }}</h3>
                        <p>{{ post.content }}</p>
                    </div>
                    <div class="post-buttons">
                        <button @click="updatePost(post)" class="post-button update-post">Изменить пост</button>
                        <button @click="deletePost(post)" class="post-button delete-post">Удалить пост</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button @click="createPost"
            class="fixed bottom-4 right-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
        Добавить пост
    </button>
    <PostModal
        :is-open="isPostModalOpen"
        :post="currentPost"
        @close="closePostModal"
    />
</template>
