<script setup>
import SingleChoice from '../components/Questions/SingleChoice.vue';
import MultipleChoice from '../components/Questions/MultipleChoice.vue';
import TrueFalse from '../components/Questions/TrueFalse.vue';
import TextAnswer from '../components/Questions/TextAnswer.vue';

import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const test = ref(null);
const answers = ref({});
const isSubmitting = ref(false);
const errorMessage = ref('');

const questionTypes = [
  { text: 'Single Choice', value: 'single_choice', component: SingleChoice },
  { text: 'Multiple Choice', value: 'multiple_choice', component: MultipleChoice },
  { text: 'True/False', value: 'true_false', component: TrueFalse },
  { text: 'Text Answer', value: 'text', component: TextAnswer }
];

const getXsrfToken = () => {
  return document.cookie
    .split('; ')
    .find(row => row.startsWith('XSRF-TOKEN='))
    ?.split('=')[1];
};

const loadTest = async () => {
  try {
    const response = await fetch(`http://localhost:8000/api/tests/${route.params.id}/questions`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${authStore.token}`
      },
      credentials: 'include'
    });

    const responseText = await response.text();
    let data;
    try {
      data = JSON.parse(responseText);
    } catch {
      console.error("Server returned non-JSON:", responseText);
      throw new Error("Server returned an invalid response");
    }

    if (!response.ok) {
      if (response.status === 401) {
        authStore.logout();
        router.push('/login');
        return;
      }
      throw new Error(data.message || `HTTP error! status: ${response.status}`);
    }

    test.value = {
      title: data.test_title || 'Untitled Test',
      description: data.test_description || '',
      questions: data.questions.map(question => ({
        id: question.id,
        type: question.type,
        question_text: question.question_text,
        options: question.options || (question.type === 'true_false' ? ['False', 'True'] : []),
        order_index: question.order_index || 0
      }))
    };

    // Initialize answers with proper structure
    test.value.questions.forEach(question => {
      answers.value[question.id] = question.type === 'text' ? '' : [];
    });

  } catch (error) {
    errorMessage.value = `Failed to load test: ${error.message}`;
    console.error("Test loading error:", error);
  }
};

const updateAnswer = (questionId, value) => {
  answers.value[questionId] = value;
};

const submitTest = async () => {
  isSubmitting.value = true;
  errorMessage.value = '';

  try {
    // Collect all answers in the format expected by backend
    const submissionData = {
      answers: test.value.questions.map(question => ({
        question_id: question.id,
        answer: question.type === 'text' 
          ? answers.value[question.id] 
          : answers.value[question.id].join(',')
      }))
    };

    console.log('Submitting test answers:', submissionData);

    await authStore.getToken();

    const response = await fetch(`http://localhost:8000/api/tests/${route.params.id}/submit`, {
      method: 'POST',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${authStore.token}`,
        'X-XSRF-TOKEN': getXsrfToken()
      },
      body: JSON.stringify(submissionData)
    });

    if (response.status === 401) {
      authStore.logout();
      router.push('/login');
      return;
    }

    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.message || 'Failed to submit test');
    }

    const result = await response.json();
    router.push(`/test-result/${result.id}`);

  } catch (error) {
    errorMessage.value = error.message;
    console.error('Submission error:', error);
  } finally {
    isSubmitting.value = false;
  }
};

onMounted(async () => {
  try {
    await authStore.getUser();
    
    if (!authStore.user) {
      router.push('/login');
      return;
    }

    await loadTest();
  } catch (error) {
    errorMessage.value = 'Failed to initialize: ' + error.message;
    console.error('Initialization error:', error);
  }
});
</script>

<template>
  <div class="min-h-full bg-gray-100 p-4">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-4xl mx-auto">
      <v-btn @click="router.go(-1)" icon="mdi-arrow-left" class="mb-4"/>
      
      <div v-if="test">
        <h1 class="text-2xl font-bold mb-2">{{ test.title }}</h1>
        <p class="text-gray-600 mb-6">{{ test.description }}</p>

        <div class="space-y-6 mb-8">
          <div 
            v-for="question in test.questions" 
            :key="question.id" 
            class="bg-gray-50 p-4 rounded-lg"
          >
            <component 
              :is="questionTypes.find(t => t.value === question.type).component"
              :questionText="question.question_text"
              :options="question.options"
              :editable="false"
              @update:modelValue="val => updateAnswer(question.id, val)"
            />
          </div>
        </div>

        <v-btn 
          @click="submitTest" 
          color="primary" 
          size="large"
          :loading="isSubmitting"
          class="w-full"
        >
          Submit Test
        </v-btn>

        <p v-if="errorMessage" class="text-red-500 mt-4">{{ errorMessage }}</p>
      </div>

      <div v-else class="text-center py-8">
        <v-progress-circular indeterminate color="primary" />
      </div>
    </div>
  </div>
</template>