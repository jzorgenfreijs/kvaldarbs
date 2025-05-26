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
      },
      password (value) {
        if (value?.length >= 8) return true

        return 'Please enter your password'
      },
    },
  })
  
  const email = useField('email')
  const password = useField('password')
  const show_pass = ref(false)

  const submit = handleSubmit(async () => {
    await authStore.handleLogin({
      email: email.value.value,
      password: password.value.value
    });
  });
  
</script>


<template>
  <v-container fluid class="d-flex justify-center align-center h-screen bg-black">
    <v-card class="pa-6" min-width="375" elevation="10" color="grey-darken-4">
      <v-card-title class="px-1"><v-btn icon="mdi-arrow-left" :width="30" :height="30" rounded="lg" class="mr-2" @click="router.push('/')"></v-btn>Log In</v-card-title>

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

        <v-text-field
          v-model="password.value.value"
          :append-inner-icon="show_pass ? 'mdi-eye' : 'mdi-eye-off'"
          :error-messages="password.errorMessage.value"
          :readonly="loading"
          :rules="[required]"
          :type="show_pass ? 'text' : 'password'"
          label="Password"
          placeholder="Enter your password"
          density="comfortable"
          @click:append-inner="show_pass = !show_pass"
          class="my-2"
        ></v-text-field>
        

        <v-btn
          class="me-4 my-2"
          type="submit"
          color="#2d815c"
        >
          Log In
        </v-btn>

        <p><a class="cursor-pointer">Forgot password?</a></p>
        <p>Don't have an account? <router-link to="/register">Register</router-link></p>
  
      </form>
    </v-card>
  </v-container>
</template>