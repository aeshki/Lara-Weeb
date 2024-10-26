<script setup>
import useAxios from '@/utils/useAxios';
import { haveFlag } from '@/utils/badge';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { ref } from 'vue';
import UserAvatar from '@/components/User/Avatar.vue';
import { Trash2, Pen, Heart } from 'lucide-vue-next';
import { EyeOff } from 'lucide-vue-next';
import { Eye } from 'lucide-vue-next';

const router = useRouter();
const authStore = useAuthStore();

const emit = defineEmits(['liked', 'unliked', 'deleted']);

const props = defineProps({
    id: [String, Number],
    initialMessage: String,
    image: String,
    author: Object,
    createdAt: String,
    visible: {
        type: Boolean,
        default: true
    },
    initialLikesCount: { type: [String, Number], default: 0 },
    latestComment: Object,
    initialLikedState: { type: Boolean, default: false },
    withUser: { type: Boolean, default: true }
});

const visibility = ref(props.visible);
const message = ref(props.initialMessage);
const likesCount = ref(props.initialLikesCount);
const isLiked = ref(props.initialLikedState);

// PERMISSIONS
const canDeletePost = authStore.user.id === props.author.id || haveFlag(authStore.user.flags, 'STAFF');
const canChangeVisibility = authStore.user.id === props.author.id || haveFlag(authStore.user.flags, 'STAFF');

const parseMessage = (message) => {
    const tagRegex = /#(\w+)/g;
    const urlRegex = /https?:\/\/[^\s]+/g;
    const spotifyRegex = /https?:\/\/open\.spotify\.com\/(?:intl-\w{2}\/)?track\/(\w+)/;

    const parts = [];
    let lastIndex = 0;
    let match;

    while ((match = urlRegex.exec(message) || tagRegex.exec(message)) !== null) {
        if (match.index > lastIndex) {
            parts.push({ value: message.slice(lastIndex, match.index), isTag: false, isUrl: false, isSpotify: false });
        }

        if (match[0].startsWith('#')) {
            parts.push({ value: match[0], isTag: true, isUrl: false, isSpotify: false });
        } else if (spotifyRegex.test(match[0])) {
            const trackId = match[0].match(spotifyRegex)[1];
            const embedUrl = `https://open.spotify.com/embed/track/${trackId}?utm_source=laraweeb`;
            parts.push({ value: embedUrl, isTag: false, isUrl: false, isSpotify: true });
        } else {
            parts.push({ value: match[0], isTag: false, isUrl: true, isSpotify: false });
        }

        lastIndex = match.index + match[0].length;
    }

    if (lastIndex < message.length) {
        parts.push({ value: message.slice(lastIndex), isTag: false, isUrl: false, isSpotify: false });
    }

    return parts;
};

const handleDelete = () => {
    const { onResolve } = useAxios(`/api/posts/${props.id}`, { method: 'DELETE' });
    onResolve(() => {
        emit('deleted', props.id);
    });
};

const toggleLike = () => {
    if (isLiked.value) {
        const { onFulfilled } = useAxios(`/api/posts/${props.id}/like`, { method: 'DELETE' });
        onFulfilled(() => {
            likesCount.value--;
            isLiked.value = false;
            emit('unliked', props.id);
        });
    } else {
        const { onFulfilled } = useAxios(`/api/posts/${props.id}/like`, { method: 'POST' });
        onFulfilled(() => {
            likesCount.value++;
            isLiked.value = true;
            emit('liked', props.id);
        });
    }
};

const handleToggleVisibility = () => {
    const { fetchRequest, onFulfilled } = useAxios(`/api/posts/${props.id}`, {
        method: 'POST',
        deferred: true,
    });

    let newVisibility = !visibility.value;

    fetchRequest({
        _method: 'PATCH',
        visible: newVisibility
    });

    onFulfilled(() => {
        visibility.value = newVisibility;
    });
};

const redirectToPost = () => router.push(`/posts/${props.id}`);
const redirectToProfile = () => router.push(`/@${props.author.username}`);
</script>

<template>
    <div
        class="flex flex-col gap-2 p-2 bg-zinc-800 border-neutral-600 rounded-lg border"
        @click="redirectToPost"
    >
        <div class="flex gap-2">
            <div @click.stop="redirectToProfile">
                <UserAvatar v-if="withUser" :username="author.username" :path="author.avatar" />
            </div>

            <div class="flex flex-col">
                <div class="flex gap-1 items-center">
                    <p
                        v-if="withUser"
                        class="text-white"
                        @click.stop="redirectToProfile"
                    >{{ author.pseudo ?? author.username }}</p>
                    <span class="text-neutral-400 text-xs">
                        {{ new Date(createdAt).toLocaleDateString('default', {
                            month: 'long',
                            year: 'numeric',
                            day: 'numeric',
                            hour: 'numeric',
                            minute: 'numeric'
                        }) }}
                    </span>
                </div>
                <span
                    v-if="withUser"
                    class="text-zinc-400 w-fit text-xs"
                    @click.stop="redirectToProfile"
                >@{{ author.username }}</span>
            </div>
        </div>

        <div class="bg-zinc-700 border border-neutral-500 p-2 rounded-lg">
            <p class="break-words">
                <template
                    v-for="(part, idx) in parseMessage(message)"
                    :key="idx"
                >
                    <RouterLink
                        v-if="part.isTag"
                        class="text-indigo-300"
                        :to="`/search?tag=${part.value.replace('#', '')}`"
                        @click.stop
                    >{{ part.value }}</RouterLink>
                    <a
                        v-else-if="part.isUrl && !part.isSpotify"
                        class="text-blue-400 underline"
                        :href="part.value"
                        target="_blank"
                        rel="noopener noreferrer"
                    >{{ part.value }}</a>
                    <iframe
                        v-else-if="part.isSpotify"
                        :src='part.value'
                        class='mt-2'
                        width='100%'
                        height='152'
                        style='border-radius:12px'
                        frameborder='0'
                        allowfullscreen
                        allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                        loading='lazy'
                    ></iframe>
                    <template v-else>{{ part.value }}</template>
                </template>
            </p>
            <img
                v-if="image"
                class="max-h-80 mt-1 w-fit rounded-xl border border-zinc-500"
                :src="`/storage/posts/${image}`"
                alt="image"
            />
        </div>
        <div
            class='flex justify-between items-center'
        >
            <div
                v-if='visibility'
                class="flex gap-1 justify-center font-bold"
                @click.stop="toggleLike"
            >
                <Heart v-if="isLiked" fill="#ff3d3d" stroke="#ff3d3d" />
                <Heart v-else strokeWidth="1.5" stroke="#ff3d3d" />
                <span v-if="likesCount">{{ likesCount }}</span>
            </div>
            <div
                v-else
                class='bg-zinc-700 border border-zinc-500 py-1 px-2 text-xs w-fit rounded-lg'
            >
                <p>Poste masqué</p>
            </div>
            
            <div
                v-if="canDeletePost || canChangeVisibility"
                class="flex gap-2 *:text-neutral-400 hover:*:text-neutral-300 *:transition-all *:duration-100"
            >
                <template v-if='canChangeVisibility'>
                    <EyeOff
                        v-if='visibility'
                        size='18'
                        @click.stop="handleToggleVisibility()"
                    />
                    <Eye
                        v-else
                        size='18'
                        @click.stop='handleToggleVisibility()'
                    /> 
                </template>
                <Trash2
                    v-if="canDeletePost"
                    size="18"
                    @click.stop="handleDelete"
                />
            </div>
        </div>
    </div>
</template>
