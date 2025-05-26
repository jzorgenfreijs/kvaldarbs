<script setup>
import { ref } from 'vue'
import { useField, useForm } from 'vee-validate'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const router = useRouter()
const show_pass = ref(false)
const loading = ref(false)

const { handleSubmit } = useForm({
  validationSchema: {
    current_password(value) {
      if (!value) return 'Current password is required'
      return true
    },
    new_password(value, ctx) {
      if (!value || value.length < 8) return 'New password must be at least 8 characters'
      if (value === ctx.form.current_password) return 'New password must be different from current password'
      return true
    },
    password_confirmation(value, ctx) {
      if (value !== ctx.form.new_password) return 'Passwords do not match'
      return true
    },
  },
})

const current_password = useField('current_password')
const new_password = useField('new_password')
const password_confirmation = useField('password_confirmation')

const submit = handleSubmit(async () => {
  loading.value = true
  try {
    await authStore.handleChangePassword({
      current_password: current_password.value.value,
      new_password: new_password.value.value,
      new_password_confirmation: password_confirmation.value.value,
    })
    router.push('/login')
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <v-container fluid class="d-flex justify-center align-center h-screen bg-black">
    <v-card class="pa-6" min-width="375" elevation="10" color="grey-darken-4">
      <v-card-title class="px-1">
        <v-btn icon="mdi-arrow-left" :width="30" :height="30" rounded="lg" class="mr-2" @click="router.push('/')"></v-btn>
        Change Password
      </v-card-title>

      <form @submit.prevent="submit">
        <v-text-field
          v-model="current_password.value.value"
          :append-inner-icon="show_pass ? 'mdi-eye' : 'mdi-eye-off'"
          :error-messages="current_password.errorMessage.value"
          :type="show_pass ? 'text' : 'password'"
          label="Current Password"
          placeholder="Enter your current password"
          density="comfortable"
          @click:append-inner="show_pass = !show_pass"
          class="my-2"
        ></v-text-field>

        <v-text-field
          v-model="new_password.value.value"
          :append-inner-icon="show_pass ? 'mdi-eye' : 'mdi-eye-off'"
          :error-messages="new_password.errorMessage.value"
          :type="show_pass ? 'text' : 'password'"
          label="New Password"
          placeholder="Enter your new password"
          density="comfortable"
          @click:append-inner="show_pass = !show_pass"
          class="my-2"
        ></v-text-field>

        <v-text-field
          v-model="password_confirmation.value.value"
          :error-messages="password_confirmation.errorMessage.value"
          type="password"
          label="Confirm New Password"
          placeholder="Re-enter your new password"
          density="comfortable"
          class="my-2"
        ></v-text-field>

        <v-btn :loading="loading" class="me-4 my-2" type="submit" color="#2d815c">
          Change Password
        </v-btn>
      </form>
    </v-card>
  </v-container>
</template>