<script setup>
import HeaderBar from '../components/HeaderBar.vue';
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const showDeleteDialog = ref(false);

onMounted(async () => {
  await authStore.getUser();
});

function handleRedirect() {
  if (!authStore.user) {
    router.push('/login');
  }
}

async function deleteAccount() {
  await authStore.handleDeleteAccount();
}
</script>

<template class="overflow-y-hidden">
  <div class="!sticky top-0 z-50 shadow-md">
    <HeaderBar />
  </div>
  <div v-if="authStore.user" class="h-[calc(100%-65px)] w-full bg-gray-100 flex content-start justify-center">
    <div class="w-[90%] lg:w-[65%] h-full bg-gray-300 left-right-shadow">
      <div class="bg-gray-200 w-full h-64 p-6 flex justify-between">
        <div class="flex flex-col justify-end">
          <v-avatar image="https://api.jzorgenfreijs.com/storage/frontend-pics/picture-placeholder.png" size="100" class="ml-2"></v-avatar>
          <v-list-item class="text-black">
            <v-list-item-title class="!text-2xl">{{ authStore.user.name }}</v-list-item-title>
            <v-list-item-subtitle class="!text-lg">{{ authStore.user.email }}</v-list-item-subtitle>
          </v-list-item>
        </div>
        <div class="flex flex-column">
          <v-btn
            class="m-1"
            color="#6b5eff"
            @click="router.push('/change-password')"
          >Reset Password</v-btn>

          <v-btn
            class="m-1"
            color="red"
            @click="showDeleteDialog = true"
          >Delete Account</v-btn>
        </div>
      </div>
    </div>
  </div>

  <div v-else>
    <div v-show="handleRedirect()"></div>
  </div>

  <v-dialog v-model="showDeleteDialog" max-width="400">
    <v-card>
      <v-card-title class="text-h6">Are you sure?</v-card-title>
      <v-card-text>
        This action will permanently delete your account and cannot be undone.
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn text @click="showDeleteDialog = false">Cancel</v-btn>
        <v-btn color="red" text @click="deleteAccount">Delete</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

</template>

<style scoped>
.left-right-shadow {
  box-shadow: -4px 0 8px rgba(0, 0, 0, 0.3), 4px 0 8px rgba(0, 0, 0, 0.3);
}
</style>
