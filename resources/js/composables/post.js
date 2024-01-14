import {ref} from "vue";
import axios from "axios";

export default function useChat() {
    const posts = ref([]);
    const errors = ref([]);
    const addPost = async (form) => {
        errors.value = [];
        try {
            const response = await axios.post('/add-post', {
                content: form.content,
                title:form.title
            });
        } catch (e) {
            if (e.response.status === 422) {
                errors.value = e.response.data.errors;
            }
            console.error('Ошибка при отправке сообщения:', e);
        }
    }

    const updatePost = async (form, post) => {
        errors.value = [];
        try {
            console.log(post)
            const response = await axios.post('/update-post', {
                postId:post.id,
                content: form.content,
                title:form.title
            });
        } catch (e) {
            if (e.response.status === 422) {
                errors.value = e.response.data.errors;
            }
            console.error('Ошибка при отправке сообщения:', e);
        }
    }

    return {
        posts,
        errors,
        addPost,
        updatePost
    };
}
