<script setup lang="js">
  import { ref } from 'vue'
  import { useField, useForm } from 'vee-validate'
  import { useAuthStore } from '../stores/auth';

  const authStore = useAuthStore();

  const { handleSubmit, handleReset } = useForm({
    validationSchema: {
      name (value) {
        if (value?.length >= 2) return true

        return 'Name needs to be at least 2 characters.'
      },
      email (value) {
        if (/^[a-z0-9.-]+@[a-z.-]+\.[a-z]+$/i.test(value)) return true;

        return 'Must be a valid e-mail.'
      },
      password (value) {
        if (value?.length >= 8) return true

        return 'Password must contain 8 characters.'
      },
      confirm_pass (value) {
        if (value == password.value.value) return true

        return 'Password do not match.'
      },
    },
  })
  const name = useField('name')
  const email = useField('email')
  const password = useField('password')
  const confirm_pass = useField('confirm_pass')

  const submit = handleSubmit(async (values) => {
    await authStore.handleRegister({
      name: name.value.value,
      email: email.value.value,
      password: password.value.value,
      password_confirmation: confirm_pass.value.value
    });
  });

</script>

<template>
  <v-container fluid class="d-flex justify-center align-center h-screen bg-black">
    <v-card class="pa-6" min-width="375" elevation="10" color="grey-darken-4">
      <v-card-title class="px-1">Registration</v-card-title>

      <form @submit.prevent="submit">
        <v-text-field
          v-model="name.value.value"
          :error-messages="name.errorMessage.value"
          label="Full Name"
          placeholder="John Doe"
          density="comfortable"
          class="mb-auto"
        ></v-text-field>
    
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
          :error-messages="password.errorMessage.value"
          :rules="[required]"
          label="Password"
          type="password"
          placeholder="Enter your password"
          density="comfortable"
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
        
        <!-- <v-divider class="border-opacity-50 mb-3"></v-divider> -->

        <v-btn
          class="me-4 my-2"
          type="submit"
          color="#2d815c"
        >
          Register
        </v-btn>

        <p>Already have an account? <router-link to="/login">Log In</router-link></p>
  
      </form>
    </v-card>
  </v-container>
</template>