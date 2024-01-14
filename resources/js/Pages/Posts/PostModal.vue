<script setup>
import {ref, watch} from "vue";
import usePost from "@/composables/post.js";
import '/resources/css/posts/index.css';

const props = defineProps({
    isOpen: Boolean,
    post: Object
});

const emit = defineEmits(['close']);

const {addPost, updatePost} = usePost();

const form = ref({
    title: '',
    content: ''
});

watch(() => props.post, (newPost) => {
    if (newPost) {
        form.value.title = newPost.title;
        form.value.content = newPost.content;
    } else {
        form.value.title = '';
        form.value.content = '';
    }
}, {immediate: true});

const handleSubmit = async (post) => {
    if (props.post.value !== null) {
        await updatePost(form.value, post);
    } else {
        await addPost(form.value);
    }
    emit('close');
};

const closePopup = () => {
    emit('close');
};
</script>

<template>
    <div v-if="isOpen" class="overlay">
        <div class="modal">
            <div class="modal-header">
                <h3 v-if="props.post.value!==null">Обновление поста</h3>
                <h3 v-else>Создание поста</h3>
                <button class="close" @click="closePopup">&times;</button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="handleSubmit(props.post)">
                    <div class="form-group">
                        <label for="postTitle">Заголовок:</label>
                        <input type="text" id="postTitle" v-model="form.title" required>
                    </div>
                    <div class="form-group">
                        <label for="postContent">Содержание:</label>
                        <textarea id="postContent" rows="4" v-model="form.content" required></textarea>
                    </div>
                    <button type="submit" v-if="props.post.value!==null" class="submit-btn">Обновить пост</button>
                    <button type="submit" v-else class="submit-btn">Создать пост</button>
                </form>
            </div>
        </div>
    </div>
</template>
