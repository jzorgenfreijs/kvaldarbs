<script setup>
import HeaderBar from '../components/HeaderBar.vue';
import TestListItem from '../components/TestListItem.vue';
import TeacherTestView from '../components/TeacherTestView.vue';

import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import axios from 'axios';

const router = useRouter();
const authStore = useAuthStore();
const tests = ref([]);
const isLoading = ref(true);
const error = ref(null);
const selectedTest = ref(null);
const showTestDialog = ref(false);
const completedTests = ref([]);

const fetchTests = async () => {
  try {
    const [testsResponse, completedResponse] = await Promise.all([
      axios.get('http://localhost:8000/api/tests', {
        headers: {
          'Authorization': `Bearer ${authStore.token}`
        }
      }),
      authStore.user?.role === 'student' ?
        axios.get('http://localhost:8000/api/student/completed-tests', {
          headers: {
            'Authorization': `Bearer ${authStore.token}`
          }
        }) : Promise.resolve({ data: [] })
    ]);

    tests.value = testsResponse.data;
    completedTests.value = completedResponse.data;
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load tests';
  } finally {
    isLoading.value = false;
  }
};

const handleTestClick = (test) => {
  if (authStore.user?.role === 'teacher') {
    selectedTest.value = test;
    showTestDialog.value = true;
  } else {
    router.push(`/test/${test.id}`);
  }
};

onMounted(async () => {
  await authStore.getUser();
  if (!authStore.user) {
    router.push('/login');
  }
  await fetchTests();
});
</script>

<template>
  <div v-if="authStore.user">
    <HeaderBar />
    <div class="!h-[calc(100vh-64px)] bg-gray-100 text-black overflow-y-auto">

      <div v-if="authStore.user.role === 'teacher'" class="h-full">
        <div id="top" class="flex justify-end">
          <v-tooltip text="Add Test" location="bottom">
            <template v-slot:activator="{ props }">
              <v-btn v-bind="props" icon="mdi-plus" class="my-4 mx-6" @click="router.push('/create-test')"></v-btn>
            </template>
          </v-tooltip>
        </div>
        <v-divider class="border-4 border-black mb-3"></v-divider>

        <div class="container mx-auto px-4">
          <h2 class="text-2xl font-bold mb-6 text-black">Your Tests</h2>

          <div v-if="isLoading" class="flex justify-center py-8">
            <v-progress-circular indeterminate color="primary"></v-progress-circular>
          </div>

          <div v-else-if="error" class="text-red-500 p-4 bg-red-50 rounded-md mb-4">
            {{ error }}
          </div>

          <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <TestListItem
              v-for="test in tests"
              :key="test.id"
              :test="test"
              @click="handleTestClick(test)"
            />
          </div>

          <div v-if="!isLoading && tests.length === 0" class="text-center py-8 text-gray-500">
            No tests found. Create your first test!
          </div>
        </div>
      </div>

      <div v-else class="container mx-auto px-4 py-6">
        <h2 class="text-2xl font-bold mb-6">Your Enrolled Tests</h2>

        <div v-if="isLoading" class="flex justify-center py-8">
          <v-progress-circular indeterminate color="primary"></v-progress-circular>
        </div>

        <div v-else-if="error" class="text-red-500 p-4 bg-red-50 rounded-md mb-4">
          {{ error }}
        </div>

        <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          <TestListItem
            v-for="test in tests.filter(t => t.enrollment_status === 'enrolled')"
            :key="test.id"
            :test="test"
            @click="handleTestClick(test)"
          />
        </div>

        <div v-if="!isLoading && tests.filter(t => t.enrollment_status === 'enrolled').length === 0"
             class="text-center py-8 text-gray-500">
          You haven't enrolled in any tests yet.
        </div>

        <h2 class="text-2xl font-bold mt-12 mb-6">Completed Tests</h2>
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          <div
            v-for="completed in completedTests"
            :key="completed.id"
            class="bg-white p-4 rounded-lg shadow hover:shadow-md transition"
          >
            <h3 class="text-lg font-semibold mb-2">{{ completed.title }}</h3>
            <p class="text-sm text-gray-600 mb-1">{{ completed.description }}</p>
            <p class="text-sm text-gray-500">Completed: {{ new Date(completed.completed_at).toLocaleDateString() }}</p>
            <p class="text-xl font-bold text-green-600 mt-2">
              {{ completed.grade_percentage !== null ? completed.grade_percentage + '%' : 'Not Graded Yet' }}
            </p>
          </div>
        </div>

        <div v-if="!isLoading && completedTests.length === 0" class="text-center py-8 text-gray-500">
          You haven't completed any tests yet.
        </div>
      </div>
    </div>

    <v-dialog v-model="showTestDialog" max-width="600">
      <TeacherTestView
        v-if="selectedTest"
        :test="selectedTest"
        @close="showTestDialog = false"
      />
    </v-dialog>
  </div>
</template>
