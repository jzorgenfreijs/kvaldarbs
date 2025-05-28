<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const router = useRouter();

const props = defineProps({
  test: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['close']);
const authStore = useAuthStore();
const showEnrollInput = ref(false);
const emailInput = ref('');
const isLoading = ref(false);
const errorMessage = ref('');

const close = () => {
  emit('close');
};

const toggleEnrollInput = () => {
  showEnrollInput.value = !showEnrollInput.value;
  emailInput.value = '';
  errorMessage.value = '';
};

const enrollStudents = async () => {
  if (!emailInput.value.trim()) {
    errorMessage.value = 'Please enter at least one email';
    return;
  }

  isLoading.value = true;
  errorMessage.value = '';

  try {
    const emails = emailInput.value.split(',')
      .map(email => email.trim())
      .filter(email => email.length > 0);

    await axios.post(
      `https://api.jzorgenfreijs.com/api/tests/${props.test.id}/assign`,
      { emails },
      {
        headers: {
          'Authorization': `Bearer ${authStore.token}`
        }
      }
    );

    alert('Students enrolled successfully!');
    toggleEnrollInput();
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Failed to enroll students';
    console.error('Enrollment error:', error);
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <v-card class="!p-2">
    <v-card-title class="text-h5 pl-6">
      {{ test.title }}
    </v-card-title>
    
    <v-card-text>
      <p class="text-gray-700 mb-4">{{ test.description }}</p>
      <p class="text-sm text-gray-500">Questions: {{ test.question_count }}</p>
      <p class="text-sm text-gray-500">Created: {{ new Date(test.created_at).toLocaleDateString() }}</p>

      <div v-if="showEnrollInput" class="mt-6">
        <v-textarea
          v-model="emailInput"
          label="Student emails (comma separated)"
          placeholder="student1@example.com, student2@example.com"
          rows="3"
          auto-grow
          outlined
          class="mb-4"
        ></v-textarea>

        <div class="flex justify-end gap-2">
          <v-btn color="gray" @click="toggleEnrollInput">
            Cancel
          </v-btn>
          <v-btn 
            color="primary" 
            @click="enrollStudents"
            :loading="isLoading"
          >
            Enroll
          </v-btn>
        </div>

        <p v-if="errorMessage" class="text-red-500 mt-2 text-sm">
          {{ errorMessage }}
        </p>
      </div>
    </v-card-text>

    <v-card-actions class="justify-end">
      <v-btn color="primary" @click="router.push(`/test/${props.test.id}/grade`)">
        View Submissions
      </v-btn>
      <v-btn 
        color="secondary" 
        @click="showEnrollInput ? toggleEnrollInput() : toggleEnrollInput()"
      >
        {{ showEnrollInput ? 'Hide Enrollment' : 'Enroll Students' }}
      </v-btn>
      <v-btn color="gray" @click="close">
        Close
      </v-btn>
    </v-card-actions>
  </v-card>
</template>