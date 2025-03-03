<script setup>
  import { ref } from 'vue'
  import { useRouter } from 'vue-router';
  import { useField, useForm } from 'vee-validate'

  const router = useRouter();

  const { handleSubmit, handleReset } = useForm({
    validationSchema: {
      name (value) {
        if (value?.length >= 2) return true

        return 'Name needs to be at least 2 characters.'
      },
      phone (value) {
        if (/^[0-9-]{7,}$/.test(value)) return true

        return 'Phone number needs to be at least 8 digits.'
      },
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
  const name = useField('name')
  const phone = useField('phone')
  const email = useField('email')
  const password = useField('password')

  const items = ref([
    'Item 1',
    'Item 2',
    'Item 3',
    'Item 4',
  ])

  const submit = handleSubmit(values => {
    alert(JSON.stringify(values, null, 2))
  })
</script>


<template>
  <v-container fluid class="d-flex justify-center align-center h-screen bg-black">
    <v-card class="pa-6" min-width="375" elevation="10" color="grey-darken-4">
      <v-card-title class="px-1">Registration</v-card-title>

      <form @submit.prevent="submit">
        <v-text-field
          v-model="name.value.value"
          :error-messages="name.errorMessage.value"
          label="Name"
          placeholder="John Doe"
          density="comfortable"
          class="mb-auto"
        ></v-text-field>
    
        <v-text-field
          v-model="phone.value.value"
          :error-messages="phone.errorMessage.value"
          label="Phone Number"
          density="comfortable"
          class="my-2"
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
          :readonly="loading"
          :rules="[required]"
          label="Password"
          placeholder="Enter your password"
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