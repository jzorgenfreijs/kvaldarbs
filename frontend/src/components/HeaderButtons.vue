<script setup>
import { useRouter } from 'vue-router';

const router = useRouter();
</script>

<template>
    <v-btn
        color="#6557ff"
        @click="router.push('/login')"
    >Login</v-btn>
</template>