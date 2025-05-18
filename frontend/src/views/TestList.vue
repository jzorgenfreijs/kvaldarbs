<script setup>
import HeaderBar from '../components/HeaderBar.vue';
import TestListItem from '../components/TestListItem.vue';
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import axios from 'axios';

const authStore = useAuthStore();

const tests = ref([]);
const isLoading = ref(true);
const error = ref(null);
const searchQuery = ref('');

const fetchPublicTests = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/public-tests', {
      params: {
        search: searchQuery.value
      }
    });
    tests.value = response.data.map(test => ({
      ...test,
      enrollment_status: null
    }));
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load public tests';
    console.error('Error fetching public tests:', err);
  } finally {
    isLoading.value = false;
  }
};

const enrollInTest = async (testId, isPublic) => {
  try {
    let password = '';

    if (authStore.user.role == 'teacher'){
      alert("Teachers can't enroll in tests!")
      return
    }

    if (!isPublic) {
      password = prompt('This is a private test. Please enter the enrollment password:') || '';
      if (password === '') {
        throw new Error('Password is required for private tests');
      }
    }
    
    await axios.post(`http://localhost:8000/api/tests/${testId}/enroll`, {
      password: password
    });
    alert('Successfully enrolled in test!');
    await fetchPublicTests();
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to enroll in test');
  }
};

onMounted(async () => {
  await authStore.getUser();

  if (authStore.user) {
    await fetchPublicTests();
  }
});
</script>

<template>
  <HeaderBar/>
  <div class="bg-gray-100 w-full h-full text-black">
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold mb-6">Available Public Tests</h1>
      <div class="mb-6">
        <v-text-field
          v-model="searchQuery"
          label="Search tests"
          prepend-inner-icon="mdi-magnify"
          clearable
          @update:modelValue="fetchPublicTests"
          @click:clear="fetchPublicTests"
        ></v-text-field>
      </div>
      
      <div v-if="isLoading" class="flex justify-center py-12">
        <v-progress-circular indeterminate size="64" color="primary"></v-progress-circular>
      </div>

      <div v-else-if="error" class="bg-red-50 text-red-600 p-4 rounded-lg mb-6">
        {{ error }}
      </div>

      <div v-else-if="tests.length === 0" class="text-center py-12 text-gray-500">
        No public tests available at the moment.
      </div>
      
      <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <div 
          v-for="test in tests" 
          :key="test.id"
          class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow"
        >
          <TestListItem :test="test" />
          <div class="p-4 border-t">
            <v-btn
              color="#6b5eff"
              block
              @click="enrollInTest(test.id, test.is_public)"
            >
              Enroll Now
            </v-btn>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>