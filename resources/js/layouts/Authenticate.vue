<script setup>
import { reactive } from 'vue';

const AllWallpapers = [
    'https://images3.alphacoders.com/132/thumb-1920-1322308.jpeg',
    'https://images7.alphacoders.com/736/thumb-1920-736462.png',
    'https://images4.alphacoders.com/133/thumb-1920-1336938.jpeg',
    'https://images2.alphacoders.com/130/thumb-1920-1307371.jpeg',
    'https://images3.alphacoders.com/131/thumb-1920-1319293.jpeg',
    'https://images7.alphacoders.com/325/thumb-1920-325547.jpg',
    'https://images6.alphacoders.com/130/thumb-1920-1308016.jpg',
    'https://images2.alphacoders.com/122/thumb-1920-1225585.jpg',
    'https://images4.alphacoders.com/972/thumb-1920-972949.jpg',
    'https://images5.alphacoders.com/131/thumb-1920-1311994.jpeg',
    'https://images5.alphacoders.com/135/thumb-1920-1354556.png',
    'https://images3.alphacoders.com/133/thumb-1920-1337364.png',
    'https://images7.alphacoders.com/134/thumb-1920-1344447.png',
    'https://images4.alphacoders.com/819/thumb-1920-819837.png'
];

const wallpaper = reactive({
    backgroundImage: `url(${AllWallpapers[Math.floor(Math.random() * AllWallpapers.length)]})`,
    backgroundRepeat: 'no-repeat',
    backgroundSize: 'cover'
});
</script>

<template>
    <main class='relative min-h-full flex justify-center mobile:items-center'>
        <RouterView />
        <div
            class='hidden absolute top-0 left-0 h-full w-full brightness-[.8] mobile:block'
            :style='wallpaper'
        ></div>
    </main>
</template>