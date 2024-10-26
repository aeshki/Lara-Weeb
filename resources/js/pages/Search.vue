<script setup>
const stringToTag = (query, withHastag = true) => {
    if (Array.isArray(query)) {
        query = query[0];
    };
    
    if (query.includes('#')) {
        query = query.replaceAll('#', '');
    };
    
    return (withHastag ? '#' : '') + query.split(/ +/).join('');
};

import { onMounted, reactive, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import useAxios from '@/utils/useAxios';

import { SearchInput, DefaultButton } from '@/components/common';
import PostCard from '@/components/PostCard.vue';
import SearchUserCard from '@/components/SearchUserCard.vue';

const route = useRoute();
const router = useRouter();

const data = ref([]);
const search = ref('');

const query = reactive({
    type: null,
    value: ''
});

watch(query, () => {
    if (!query.type) {
        return;
    }

    if (query.type === 'tag') {
        const { onFulfilled } = useAxios(`/api/posts?tag=${query.value}`);

        onFulfilled((req) => data.value = req.value.posts);
    } else if (query.type === 'user') {
        const { onFulfilled } = useAxios(`/api/users?username=${query.value}`);

        onFulfilled((req) => data.value = req.value.users);
    }
});

onMounted(() => {
    if (Object.keys(route.query).length > 0) {
        const firstQuery = Object.entries(route.query).shift();
        
        if (firstQuery[0] === 'tag') {
            query.type = 'tag';
            query.value = stringToTag(firstQuery[1], false);
            search.value = stringToTag(firstQuery[1]);
        } else if (firstQuery[0] === 'user') {
            query.type = 'user';
            search.value = query.value = firstQuery[1];
        }
    }
});

const handleClick = () => {
    if (search.value.length < 2) {
        query.type = null;
        query.value = '';

        data.value = [];

        router.push({ path: '/search' });
        return;
    }

    query.type = search.value.startsWith('#') ? 'tag' : 'user';

    if (query.type === 'tag') {
        search.value = stringToTag(search.value);
        query.value = stringToTag(search.value, false);
    } else {
        query.value = search.value
    }

    router.push({
        path: '/search',
        query: { [query.type]: query.value }
    });
};

const postTagClicked = (tag) => {
    search.value = tag;
    handleClick();
}
</script>

<template>
    <ul class='flex flex-col items-center gap-4 p-4'>
        <SearchInput
            min-length='2'
            v-model='search'
            @validate='handleClick()'
        />
        <template v-if='query.type === "tag"'>
            <PostCard
                v-for='post in data.data'
                :key='post.id'
                :created-at='post.created_at'
                :initial-message='post.message'
                :initial-likes-count='post.likes_count'
                :initial-liked-state='post.is_liked'
                v-bind='post'
            />
            <p v-if='query.value?.length > 0 && data.data?.length < 1'>Aucun Tag trouvé</p>
        </template>
        <template v-else-if='query.type === "user"'>
            <SearchUserCard
                v-for='user in data.data'
                :key='user.id'
                :isPrivate='user.is_private'
                v-bind='user'
            />
            <p v-if='query.value?.length > 0 && data.data?.length < 1'>Aucun Utilisateur trouvé</p>
        </template>
    </ul>
</template>