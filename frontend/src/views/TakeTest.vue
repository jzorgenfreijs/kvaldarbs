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
const testQuestions = ref([]);
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

    const data = await response.json();
    if (!response.ok) throw new Error(data.message || 'Failed to load test');

    test.value = {
      title: data.test_title,
      description: data.test_description
    };

    testQuestions.value = data.questions.map(question => ({
      id: question.id,
      component: questionTypes.find(t => t.value === question.type).component,
      type: question.type,
      data: {
        questionText: question.question_text,
        options: question.options || (question.type === 'true_false' ? ['False', 'True'] : []),
        correctAnswer: question.type === 'text' ? '' : 
                     question.type === 'true_false' ? [0] : 
                     question.type === 'single_choice' ? [] : []
      }
    }));

  } catch (error) {
    errorMessage.value = `Failed to load test: ${error.message}`;
  }
};

const submitTest = async () => {
  isSubmitting.value = true;
  errorMessage.value = '';

  try {
    await authStore.getToken()

    const submissionData = {
      answers: testQuestions.value.map(question => ({
        question_id: question.id,
        answer: question.data.correctAnswer
      }))
    };

    console.log('Submitting:', JSON.stringify(submissionData, null, 2));

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

    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.message || 'Submission failed');
    }

    const result = await response.json();
    router.push(`/my-tests`);

  } catch (error) {
    errorMessage.value = error.message;
  } finally {
    isSubmitting.value = false;
  }
};

onMounted(async () => {
  await authStore.getUser();
  if (!authStore.user) {
    router.push('/login');
  } else {
    await loadTest();
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
            v-for="question in testQuestions" 
            :key="question.id" 
            class="bg-gray-50 p-4 rounded-lg"
          >
            <component 
              :is="question.component"
              :question-text="question.data.questionText"
              :options="question.data.options"
              :correct-answer="question.data.correctAnswer"
              @update:correct-answer="val => question.data.correctAnswer = val"
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