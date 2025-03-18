<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

onMounted(async () => {
  await authStore.getUser();
});

function handleRedirect() {
  if (!authStore.user) {
    router.push('/login');
  }
}
</script>

<template>
<div v-if="authStore.user" class="h-screen bg-gray-100">

</div>
<div v-else>
  <div v-show="handleRedirect()"></div>
</div>
</template>