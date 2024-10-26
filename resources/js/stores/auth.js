import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null);

    async function getCSRFToken() {
        return await axios.get('/sanctum/csrf-cookie');
    };

    async function isAuthenticate() {
        if (!user.value) {
            return false;
        }

        const rep = await axios.get('/api/users/@me')
            .catch((err) => err.response);
        
        const isOK = rep.status === 200;

        if (isOK) {
            user.value = rep.data.user;
        } else {
            $reset();
        }

        return isOK;
    }

    async function setUser(data) {
        user.value = data;
    }

    async function updateUser(data) {
        let formData = new FormData();

        formData.set('_method', 'PATCH');

        Object.entries(data).forEach(data => {
            data[1] ??= '';

            if ((data[0] === 'avatar' || data[0] === 'banner') && !(data[1] instanceof File) && data[1]) {
                return
            } else if (typeof data[1] === 'boolean') {
                data[1] = +data[1]
            }

            formData.append(data[0], data[1]);
        })
        
        return await axios.post(`/api/users/@me`, formData)
            .then(() => {
                user.value = { ...user.value, ...formData };
                this.router.push(`/@${user.value.username}`)
            });
    }

    function $reset() {
        user.value = null;
    }

    function resetImages() {
        user.value.avatar = null;
        user.value.banner = null;
    };

    return {
        user,
        getCSRFToken,
        isAuthenticate,
        setUser,
        updateUser,
        resetImages,
        $reset
    }
}, { persist: true });