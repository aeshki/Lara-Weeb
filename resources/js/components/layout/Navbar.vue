<script setup>
import UserAvatar from '@/components/User/Avatar.vue';
import NavButton from '@/components/common/buttons/NavButton.vue';
import { Home, Search, Bell } from 'lucide-vue-next';

import { useAuthStore } from '@/stores/auth';
import { useNotificationStore } from '@/stores/notification';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

const notificationStore = useNotificationStore();

const handleRedirect = () => {
    router.push('/notifications');
}
</script>

<template>
    <aside class='p-4 bg-zinc-900 text-zinc-50 border-t border-neutral-600 sm:border-r sm:border-t-0'>
        <nav class='flex sm:flex-col gap-4 justify-around items-center md:items-start *:md:w-full'>
            <div>
                <NavButton
                    title='Accueil'
                    :icon='Home'
                    to='/'
                />
            </div>
            <div>
                <NavButton
                    title="Recherche"
                    :icon='Search'
                    to='/search'
                />
            </div>
            <div class='relative'>
                <NavButton
                    title="Notifications"
                    :icon='Bell'
                    to='/notifications'
                />
                <span
                    v-if='notificationStore.unreadCount > 0'
                    class='flex items-center justify-center absolute top-0 right-0 md:left-6 bg-rose-700 p-1 max-w-5 max-h-5 w-full h-full text-xs rounded-lg overflow-hidden'
                    :class='{ "hidden": $route.name === "Notifications" }'
                    @click='handleRedirect'
                >{{ notificationStore.unreadCount }}</span>
            </div>
            <div>
                <RouterLink
                    class='border-neutral-500/70 flex gap-2 items-center text-white md:rounded-xl rounded-full max-md:[&.exact-active]:border-4 [&.exact-active]:bg-neutral-500/70 transition-all duration-200 md:py-2 md:px-3'
                    :to='`/@${authStore.user.username}`'
                >
                    <UserAvatar
                        :path="authStore.user?.avatar"
                        :username="authStore.user.username"
                        :class='["w-8 h-8"]'
                    />
                    <span class='hidden md:block text-nowrap'>Mon profil</span>
                </RouterLink>
            </div>
        </nav>
    </aside>
</template>
