<script setup>
import NotificationCard from '@/components/NotificationCard.vue';
import { onBeforeRouteLeave } from 'vue-router';
import { useNotificationStore } from '../stores/notification';
import axios from 'axios';

const notificationStore = useNotificationStore();

onBeforeRouteLeave(() => {
    axios.post(`/api/users/@me/notifications/mark-as-read`)
        .then(() => notificationStore.markNotificationsAsRead());
});
</script>

<template>
    <main class='p-4'>
        <div class='flex flex-col gap-4'>
            <template v-if='notificationStore.notifications.length > 0'>
                <NotificationCard
                    v-for='notification in notificationStore.notifications'
                    :key='notification.id'
                    v-bind='notification'
                />
            </template>
            <p
                v-else
                class='self-center'
            >Aucune notifications.</p>
        </div>
    </main>
</template>