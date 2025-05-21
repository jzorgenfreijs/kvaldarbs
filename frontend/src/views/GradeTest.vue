<script setup>
import HeaderBar from '../components/HeaderBar.vue';
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const testId = route.params.id;

const completedTests = ref([]);
const selectedStudent = ref(null);
const testDetails = ref(null);
const studentAnswers = ref([]);
const loading = ref(true);
const maxScore = ref(0);
const allGrades = ref({});

const correctTextAnswers = ref({});

const totalScore = computed(() => {
  let score = 0;
  
  studentAnswers.value.forEach(answer => {
    if (!answer.needs_manual_grading && answer.is_correct) {
      score += answer.question_points;
    }
  });

  Object.keys(correctTextAnswers.value).forEach(questionId => {
    if (correctTextAnswers.value[questionId]) {
      const answer = studentAnswers.value.find(a => a.question_id == questionId);
      if (answer) score += answer.question_points;
    }
  });
  
  return score;
});

const fetchCompletedTests = async () => {
  try {
    loading.value = true;

    const [completionsResponse, gradesResponse] = await Promise.all([
      axios.get(`/api/tests/${testId}/completed-students`),
      axios.get(`/api/tests/${testId}/grades`)
    ]);
    
    completedTests.value = completionsResponse.data.completed_tests;
    testDetails.value = completionsResponse.data.test;

    gradesResponse.data.forEach(grade => {
      allGrades.value[grade.user_id] = grade;
    });
    
  } catch (error) {
    console.error('Error fetching data:', error);
  } finally {
    loading.value = false;
  }
};

const fetchStudentAnswers = async (userId) => {
  try {
    loading.value = true;
    const response = await axios.get(`/api/tests/${testId}/students/${userId}/answers`);
    
    studentAnswers.value = response.data.answers;
    maxScore.value = response.data.max_score;

    correctTextAnswers.value = {};
    studentAnswers.value.forEach(answer => {
      if (answer.needs_manual_grading && answer.is_correct) {
        correctTextAnswers.value[answer.question_id] = true;
      }
    });
    
  } catch (error) {
    console.error('Error fetching student answers:', error);
  } finally {
    loading.value = false;
  }
};

const selectStudent = (completedTest) => {
  selectedStudent.value = completedTest;
  fetchStudentAnswers(completedTest.student.id);
};

const markTextAnswer = (questionId, isCorrect) => {
  correctTextAnswers.value[questionId] = isCorrect;
};

const saveGrade = async () => {
  try {
    loading.value = true;
    
    const response = await axios.post(
      `/api/tests/${testId}/students/${selectedStudent.value.student.id}/grade`,
      { score: totalScore.value }
    );
    
    allGrades.value[selectedStudent.value.student.id] = {
      ...response.data,
      score: totalScore.value
    };
    
  } catch (error) {
    console.error('Error saving grade:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchCompletedTests();
});
</script>

<template>
  <div class="sticky top-0 z-50">
    <HeaderBar/>
  </div>
  
  <div class="h-[calc(100vh-64px)] flex bg-gray-100 text-black">
    <div class="w-1/3 border-r border-gray-300 bg-white overflow-y-auto p-4">
      <div class="mb-6">
        <h1 class="text-2xl font-bold">{{ testDetails?.title || 'Completed Tests' }}</h1>
        <p class="text-gray-600">{{ completedTests.length }} students completed</p>
      </div>

      <div v-if="loading" class="flex justify-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
      </div>

      <div v-else class="space-y-3">
        <div 
          v-for="completedTest in completedTests" 
          :key="completedTest.id"
          @click="selectStudent(completedTest)"
          class="p-4 border rounded-lg cursor-pointer transition-all hover:shadow-md"
          :class="{
            'border-blue-500 bg-blue-50': selectedStudent?.id === completedTest.id,
            'border-gray-200 bg-white': selectedStudent?.id !== completedTest.id
          }"
        >
          <div class="flex justify-between items-start">
            <div>
              <h3 class="font-medium">{{ completedTest.student.name }}</h3>
              <p class="text-sm text-gray-600">
                Completed: {{ new Date(completedTest.created_at).toLocaleString() }}
              </p>
            </div>
            <span 
              class="px-2 py-1 text-xs rounded-full"
              :class="{
                'bg-green-100 text-green-800': allGrades[completedTest.student.id],
                'bg-yellow-100 text-yellow-800': !allGrades[completedTest.student.id]
              }"
            >
              {{ allGrades[completedTest.student.id] 
                ? `${allGrades[completedTest.student.id].score}` 
                : 'Not graded' }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <div class="flex-1 overflow-y-auto p-6">
      <div v-if="!selectedStudent" class="flex items-center justify-center h-full">
        <div class="text-center text-gray-500">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <p class="text-lg">Select a student to view their answers</p>
        </div>
      </div>

      <div v-else>
        <div class="flex justify-between items-center mb-6">
          <div>
            <h2 class="text-xl font-bold">{{ selectedStudent.student.name }}</h2>
            <p class="text-gray-600">Completed on {{ new Date(selectedStudent.created_at).toLocaleString() }}</p>
            <p class="text-lg font-semibold mt-2">
              Current Grade: <span class="text-blue-600">{{ totalScore }}</span>/<span class="text-gray-600">{{ maxScore }}</span>
            </p>
          </div>
          <button 
            @click="saveGrade"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
            :disabled="loading"
          >
            {{ loading ? 'Saving...' : 'Save Grade' }}
          </button>
        </div>

        <div class="space-y-4">
          <div 
            v-for="(answer, index) in studentAnswers" 
            :key="answer.question_id"
            class="p-4 border rounded-lg bg-white"
          >
            <div class="flex justify-between items-start mb-3">
              <h3 class="font-medium">Question {{ index + 1 }} ({{ answer.question_points }} pts)</h3>
              <span 
                v-if="answer.is_correct !== null"
                class="px-2 py-1 text-xs rounded-full"
                :class="{
                  'bg-green-100 text-green-800': answer.is_correct,
                  'bg-red-100 text-red-800': !answer.is_correct
                }"
              >
                {{ answer.is_correct ? 'Correct' : 'Incorrect' }}
              </span>
            </div>
            
            <p class="mb-3 font-medium">{{ answer.question_text }}</p>

            <template v-if="!answer.needs_manual_grading">
              <div class="mb-3">
                <p class="text-sm font-medium text-gray-700 mb-1">Options:</p>
                <ul class="list-disc pl-5 space-y-1">
                  <li 
                    v-for="(option, optIndex) in answer.options" 
                    :key="optIndex"
                    :class="{
                      'text-green-600 font-medium': answer.correct_answer_index.includes(optIndex)
                    }"
                  >
                    {{ option }}
                  </li>
                </ul>
              </div>
              
              <div class="bg-gray-50 p-3 rounded mb-3">
                <p class="text-sm font-medium text-gray-700 mb-1">Student Answer:</p>
                <p>{{ Array.isArray(answer.student_answer_text) 
                  ? answer.student_answer_text.join(', ') 
                  : answer.student_answer_text }}</p>
              </div>
            </template>

            <template v-else>
              <div class="bg-gray-50 p-3 rounded mb-3">
                <p class="text-sm font-medium text-gray-700 mb-1">Student Answer:</p>
                <p>{{ answer.student_answer_text || 'No answer provided' }}</p>
              </div>
              
              <div class="flex space-x-2 mt-3">
                <button 
                  @click="markTextAnswer(answer.question_id, true)"
                  class="px-3 py-1 bg-green-100 text-green-800 rounded hover:bg-green-200 text-sm"
                  :class="{ 'ring-2 ring-green-500': correctTextAnswers[answer.question_id] === true }"
                >
                  Mark Correct (+{{ answer.question_points }})
                </button>
                <button 
                  @click="markTextAnswer(answer.question_id, false)"
                  class="px-3 py-1 bg-red-100 text-red-800 rounded hover:bg-red-200 text-sm"
                  :class="{ 'ring-2 ring-red-500': correctTextAnswers[answer.question_id] === false }"
                >
                  Mark Incorrect (0)
                </button>
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}
::-webkit-scrollbar-track {
  background: #f1f1f1;
}
::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 4px;
}
::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>