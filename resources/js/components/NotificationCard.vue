<script setup>
import { UserAvatar } from '@/components/User'
import axios from 'axios';
import { Trash } from 'lucide-vue-next';
import { useRouter } from 'vue-router';
import { useNotificationStore } from '../stores/notification';
import RoundedButton from '@/components/common/buttons/RoundedButton.vue';
import useAxios from '@/utils/useAxios';

const props = defineProps({
    id: [String, Number],
    type: String,
    author: Object,
    read: Boolean, 
    created_at: String,
    notifiable: Object
});

const emit = defineEmits([ 'deleted' ]);

const router = useRouter();
const notificationStore = useNotificationStore();

const handleRedirect = () => {
    if (props.type === 'liked' || props.type === 'replied' || props.type === 'posted') {
        router.push(`/posts/${props.notifiable.id}`);
    }
};

const handleDelete = () => {
    axios.delete(`/api/users/@me/notifications/${props.id}`).then(() => {
        notificationStore.removeNotification(props.id)
    });
};

const handleAccept = () => {
    const { onFulfilled } = useAxios(`api/users/${props.notifiable.followed_id}/follow-request/${props.notifiable.id}`, { method: 'POST' });

    onFulfilled(() => handleDelete());
};

const handleReject = () => {
    const { onFulfilled } = useAxios(`api/users/${props.notifiable.followed_id}/follow-request/${props.notifiable.id}`, { method: 'DELETE' });

    onFulfilled(() => handleDelete());
};
</script>

<template>
    <Transition name="slide-up" mode="out-in" appear>
        <div
            class='relative flex items-center justify-between gap-2 bg-zinc-800 p-2 border-2 border-zinc-700 rounded-lg cursor-pointer'
            @click='handleRedirect()'
        >
            <div class='flex gap-2'>
                <UserAvatar
                    :username='author.username'
                    :path='author.avatar'
                />
                <div class='flex flex-col gap-1'>
                    <span class='text-sm text-neutral-400'>{{new Date(created_at).toLocaleDateString('default', {
                                    month: 'long',
                                    year: 'numeric',
                                    day: 'numeric',
                                    hour: 'numeric',
                                    minute: 'numeric'
                    })}}</span>
                    <p>
                        <span class='font-bold'>{{ author.pseudo ?? author.username }}</span>
                        {{
                            type === 'replied'
                                ? " a répondu à votre poste."
                                : type === 'like'
                                    ? " a aimer votre poste."
                                    : type === 'pending'
                                        ? " souhaite vous suivre"
                                        : type === 'followed'
                                            ? " a commencer à vous suivre"
                                            : " viens de créer un nouveau poste"
                                        
                        }}
                    </p>
                    <div
                        v-if='type === "pending"'
                        class='flex gap-2 *:relative'
                    >
                        <RoundedButton
                            text='Refuser'
                            @click='handleReject()'
                        />

                        <RoundedButton
                            variant='fill'
                            text='Accepter'
                            @click='handleAccept()'
                        />
                    </div>
                </div>
            </div>
            <div class='flex items-center gap-3'>
                <Trash  
                    class='text-neutral-400'
                    @click.stop='handleDelete'
                />
                <span
                    v-if='!read'
                    class="relative flex h-3 w-3"
                >
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                </span>
            </div>
        </div>
    </Transition>
</template>
<style>
.btn-container {
  display: inline-block;
  position: relative;
  height: 1em;
}

button {
  position: absolute;
}

.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.25s ease-out;
}

.slide-up-enter-from {
  opacity: 0;
  transform: translateY(30px);
}

.slide-up-leave-to {
  opacity: 0;
  transform: translateY(-30px);
}
</style>