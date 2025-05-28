<script setup>
  import { ref, onMounted } from 'vue'
  import { useField, useForm } from 'vee-validate'
  import { useRouter, useRoute } from 'vue-router';
  import { useAuthStore } from '../stores/auth';

  const authStore = useAuthStore();
  const router = useRouter();
  const route = useRoute();

  onMounted(async () => {
    await authStore.getUser();
    if (authStore.user) {
      router.push('/')
    }
  });

  const { handleSubmit } = useForm({
    validationSchema: {
      password (value) {
        if (value?.length >= 8) return true

        return 'Please enter your password'
      },
      confirm_pass (value) {
        if (value == password.value.value) return true

        return 'Password do not match.'
      },
    },
  })
  const password = useField('password')
  const confirm_pass = useField('confirm_pass')
  const show_pass = ref(false)

  const submit = handleSubmit(async () => {
    await authStore.handleResetPassword({
      password: password.value.value,
      password_confirmation: confirm_pass.value.value,
      email: route.query.email,
      token: route.params.token,
    });
  });
  
</script>


<template>
  <v-container fluid class="d-flex justify-center align-center h-screen bg-gray-100">
    <v-card class="pa-6" min-width="375" elevation="10" color="grey-lighten-4">
      <v-card-title class="px-1"><v-btn icon="mdi-arrow-left" :width="30" :height="30" rounded="lg" class="mr-2" @click="router.push('/')"></v-btn>Log In</v-card-title>

      <form @submit.prevent="submit">
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
        
        <v-text-field
          v-model="confirm_pass.value.value"
          :error-messages="confirm_pass.errorMessage.value"
          :rules="[required]"
          label="Confirm Password"
          type="password"
          density="comfortable"
          class="my-2"
        ></v-text-field>

        <v-btn
          class="me-4 my-2"
          type="submit"
          color="#2d815c"
        >
          Reset
        </v-btn>
  
      </form>
    </v-card>
  </v-container>
</template>