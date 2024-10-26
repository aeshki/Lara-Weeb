import { createWebHistory, createRouter } from 'vue-router'

import {
    FeedPage,
    SearchPage,
    UserPostPage,
    UserProfilePage,
    UserSettingsPage,
    NotFoundPage,
} from '@/pages';

const routes = [
    {
        path: '/',
        component: () => import('@/layouts/App.vue'),
        children: [
            {
                path: '',
                name: 'Feed',
                component: FeedPage
            },
            {
                path: 'search',
                component: SearchPage
            },
            {
                path: 'notifications',
                name: 'Notifications',
                component: () => import('@/pages/NotificationPage.vue')
            },
            {
                path: '@:username',
                component: UserProfilePage
            },
            {
                path: 'posts',
                redirect: '/'
            },
            {
                path: 'posts/:id',
                component: UserPostPage
            },
            {
                path: 'settings',
                component: UserSettingsPage
            }
        ],
        meta: {
            requiresAuth: true
        }
    },
    {
        path: '/auth',
        component: () => import('@/layouts/Authenticate.vue'),
        children: [
            {
                path: 'login',
                component: () => import('@/components/common/forms/Login.vue')
            },
            {
                path: 'register',
                component: () => import('@/components/common/forms/Register.vue')
            },
            {
                path: 'password/reset',
                component: () => import('@/components/common/forms/ResetPassword.vue')
            },
            {
                path: ':any(.*)',
                redirect: '/auth/login'
            }, 
        ]
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'NotFound',
        component: NotFoundPage
    }
];

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    linkActiveClass: 'active',
    linkExactActiveClass: 'exact-active',
    routes,
});

import { useAuthStore } from '@/stores/auth';

router.beforeEach(async (to) => {
    const authStore = useAuthStore();
    const isAuthenticate = await authStore.isAuthenticate();

    if ((to.path === '/auth/login' || to.path === '/auth/register') && isAuthenticate) return '/'
    else if (to.matched.some((record) => record.meta.requiresAuth) && !isAuthenticate) return '/auth/login'
});

export default router;