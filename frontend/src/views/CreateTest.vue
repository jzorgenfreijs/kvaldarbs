<script setup>
import SingleChoice from '../components/Questions/SingleChoice.vue';
import MultipleChoice from '../components/Questions/MultipleChoice.vue';
import TrueFalse from '../components/Questions/TrueFalse.vue';
import TextAnswer from '../components/Questions/TextAnswer.vue';

import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const testTitle = ref('');
const testDescription = ref('');
const isPublic = ref(true);
const testPassword = ref('');
const selectedType = ref(null);
const addedQuestions = ref([]);
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

const addQuestion = (type) => {
  const questionType = questionTypes.find(t => t.text === type);
  if (!questionType) return;

  const baseData = {
    questionText: 'Double-click to edit question',
    correctAnswer: []
  };

  if (type !== 'Text Answer') {
    baseData.options = type === 'True/False' 
      ? ['True', 'False'] 
      : ['Option 1', 'Option 2', 'Option 3', 'Option 4'];
    baseData.correctAnswer = [0];
  }

  addedQuestions.value.push({
    id: Date.now(),
    component: questionType.component,
    type: questionType.value,
    data: baseData
  });

  selectedType.value = null;
};

const removeQuestion = (id) => {
  addedQuestions.value = addedQuestions.value.filter(q => q.id !== id);
};

const submitTest = async () => {
  errorMessage.value = '';
  
  if (!testTitle.value.trim()) {
    errorMessage.value = 'Please enter a test title';
    return;
  }

  if (addedQuestions.value.length === 0) {
    errorMessage.value = 'Please add at least one question';
    return;
  }

  const invalidQuestions = addedQuestions.value.filter(q => {
    return q.type === 'multiple_choice' && 
          (!q.data.correctAnswer || q.data.correctAnswer.length === 0);
  });

  if (invalidQuestions.length > 0) {
    errorMessage.value = 'Please select at least one correct answer for all multiple choice questions';
    return;
  }

  isSubmitting.value = true;

  try {
    await authStore.getToken()

    const testData = {
      title: testTitle.value,
      description: testDescription.value,
      is_public: isPublic.value,
      enrollment_password: testPassword.value || null,
      questions: addedQuestions.value.map((q, index) => ({
        type: q.type,
        question_text: q.data.questionText,
        options: q.type === 'text' ? null : q.data.options,
        correct_answers: q.type === 'multiple_choice' 
          ? q.data.correctAnswer
          : Array.isArray(q.data.correctAnswer) 
            ? q.data.correctAnswer 
            : [q.data.correctAnswer],
        points: 1,
        order_index: index + 1
      }))
    };

    console.log('Submitting test data:', testData); // Debug log

    const response = await fetch('https://api.jzorgenfreijs.com/api/tests', {
      method: 'POST',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${authStore.token}`,
        'X-XSRF-TOKEN': getXsrfToken()
      },
      body: JSON.stringify(testData)
    });

    if (response.status === 401) {
      authStore.logout();
      router.push('/login');
      return;
    }

    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.message || 'Failed to save test');
    }

    const result = await response.json();
    router.push(`/my-tests`);
    
  } catch (error) {
    errorMessage.value = error.message;
    console.error('Save error:', error);
  } finally {
    isSubmitting.value = false;
  }
};

onMounted(async () => {
  try {
    await authStore.getUser();
    if (!authStore.user) {
      router.push('/login');
    }
  } catch (error) {
    errorMessage.value = 'Failed to initialize: ' + error.message;
    console.error('Initialization error:', error);
  }
});
</script>

<template>
  <div class="min-h-full bg-gray-100 p-4">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-4xl mx-auto">
      <v-btn @click="router.push('/my-tests')" icon="mdi-arrow-left" class="mb-4"/>
      <h1 class="text-2xl font-bold mb-6">Create a Test</h1>

      <v-text-field v-model="testTitle" label="Title" class="mb-4" required/>
      <v-textarea v-model="testDescription" label="Description" class="mb-4"/>
      
      <v-checkbox v-model="isPublic" label="Public Test" class="mb-4"/>
      <v-text-field 
        v-model="testPassword" 
        label="Test Password (Optional)" 
        class="mb-6"
        :disabled="isPublic"
      />

      <div class="space-y-4 mb-6">
        <div v-for="question in addedQuestions" :key="question.id" class="relative bg-gray-100 p-4 rounded">
          <v-btn @click="removeQuestion(question.id)" icon="mdi-close" class="absolute top-2 right-2"/>
          <component 
            :is="question.component" 
            :question-text="question.data.questionText"
            :options="question.data.options"
            :correct-answer="question.data.correctAnswer"
            @update:question-text="val => question.data.questionText = val"
            @update:options="val => question.data.options = val"
            @update:correct-answer="val => question.data.correctAnswer = val"
          />
        </div>
      </div>

      <div class="mb-6">
        <p class="font-medium mb-2">Add Question:</p>
        <v-select
          v-model="selectedType"
          :items="questionTypes.map(t => t.text)"
          @update:model-value="addQuestion"
          label="Question Type"
        />
      </div>

      <v-btn 
        @click="submitTest" 
        color="primary" 
        size="large"
        class="w-full"
      >
        Create Test
      </v-btn>
    </div>
  </div>
</template>