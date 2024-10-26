import { defineStore } from 'pinia';
import { computed, ref } from 'vue';

export const useNotificationStore = defineStore('notification', () => {
    const notifications = ref([]);

    const unreadCount = computed(() => {
        return notifications.value.filter(({ read }) => !read).length;
    });

    const setNotifications = (data) => {
        notifications.value = data;
    };

    const addNotification = (notification) => {
        notifications.value.unshift(notification);
    };

    const removeNotification = (notificationId) => {
        notifications.value = notifications.value.filter((notification) => notification.id !== notificationId);
    };

    const clearNotifications = () => {
        notifications.value = [];
    };

    const markNotificationsAsRead = () => {
        notifications.value.map((notif) => {
            notif.read = true;

            return notif;
        });
    }

    return {
        notifications,
        unreadCount,
        setNotifications,
        addNotification,
        removeNotification,
        clearNotifications,
        markNotificationsAsRead
    };
});