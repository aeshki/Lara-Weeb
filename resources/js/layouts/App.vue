<script setup>
import Navbar from '@/components/layout/Navbar.vue';
import { useAuthStore } from '../stores/auth';
import { useNotificationStore } from '@/stores/notification';
import useAxios from '@/utils/useAxios';
import { onMounted } from 'vue';

const notificationStore = useNotificationStore();
const authStore = useAuthStore();

onMounted(() => {
    if (authStore.isAuthenticate()) {
        const { onFulfilled } = useAxios('/api/users/@me/notifications');

        onFulfilled((data) => {
            notificationStore.setNotifications(data.value.notifications);
        });

        Echo.private(`user.${authStore.user.id}.notifications`)
            .listen('.notification.created', (notification) => {
                notificationStore.addNotification(notification);
            });
    }
});
</script>

<template>
    <div class='flex flex-col-reverse w-full h-full overflow-hidden text-slate-50 sm:flex-row'>
        <Navbar />
        <div class='flex flex-col w-full h-full overflow-y-scroll'>
            <router-view class='w-full max-w-screen-md justify-self-center self-center'/>
        </div>
    </div>
</template>