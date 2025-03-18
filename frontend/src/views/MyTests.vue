<script setup>
import HeaderBar from '../components/HeaderBar.vue';
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
<div v-if="authStore.user">
  <HeaderBar />
  <div class="h-[calc(100%-64px)] bg-gray-100">
    <div v-if="authStore.user.role == 'teacher'" class="h-[calc(100vh-64px)]">
      <div id="top" class="flex justify-end">
        <v-tooltip text="Add Test" location="bottom">
          <template v-slot:activator="{ props }">
            <v-btn v-bind="props" icon="mdi-plus" class="my-4 mx-6" @click="router.push('/create-test')"></v-btn>
          </template>
        </v-tooltip>
      </div>
      <v-divider class="border-4 border-black mb-3"></v-divider>
    </div>
    <div v-else></div>
  </div>
</div>
<div v-else>
  <div v-show="handleRedirect()"></div>
</div>
</template>