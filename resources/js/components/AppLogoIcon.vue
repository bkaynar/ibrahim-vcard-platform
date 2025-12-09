<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <!-- buraya public/main/adanabarosu.png gelecek-->
    <img src="/main/adanabarosu.png" :class="className" alt="App Logo" />
</template>
