<script setup>
  import { ref, onMounted } from 'vue'
  import { useField, useForm } from 'vee-validate'
  import { useRouter } from 'vue-router';
  import { useAuthStore } from '../stores/auth';

  const authStore = useAuthStore();
  const router = useRouter();

  onMounted(async () => {
    await authStore.getUser();
    if (authStore.user) {
      router.push('/')
    }
  });

  const { handleSubmit } = useForm({
    validationSchema: {
      email (value) {
        if (/^[a-z0-9.-]+@[a-z.-]+\.[a-z]+$/i.test(value)) return true;

        return 'Must be a valid e-mail.'
      }
    },
  })
  
  const email = useField('email')

  const submit = handleSubmit(async () => {
    await authStore.handleForgotPass({
      email: email.value.value,
    });
  });
  
</script>


<template>
  <v-container fluid class="d-flex justify-center align-center h-screen bg-gray-100">
    <v-card class="pa-6" min-width="375" elevation="10" color="grey-lighten-4">
      <v-card-title class="px-1"><v-btn icon="mdi-arrow-left" :width="30" :height="30" rounded="lg" class="mr-2" @click="router.push('/')"></v-btn>Forgot Password</v-card-title>

      <form @submit.prevent="submit">
        <v-text-field
          v-model="email.value.value"
          :error-messages="email.errorMessage.value"
          label="E-mail"
          placeholder="example@gmail.com"
          type="email"
          density="comfortable"
          class="my-2"
        ></v-text-field>

        <v-btn
          class="me-4 my-2"
          type="submit"
          color="#2d815c"
        >
          Send Email
        </v-btn>
  
      </form>
    </v-card>
  </v-container>
</template>