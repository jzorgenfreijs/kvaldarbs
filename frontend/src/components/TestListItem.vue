<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';

const props = defineProps({
  test: {
    type: Object,
    required: true,
    default: () => ({
      id: null,
      title: '',
      description: '',
      is_public: false,
      question_count: 0,
      enrollment_status: null, // 'creator', 'enrolled', or null
      created_at: ''
    })
  }
});

const router = useRouter();

const formattedDate = computed(() => {
  return new Date(props.test.created_at).toLocaleDateString();
});

const navigateToTest = () => {
  router.push(`/test/${props.test.id}`);
};
</script>

<template>
  <div 
    @click="navigateToTest"
    class="border rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer bg-white"
  >
    <div class="flex justify-between items-start mb-2">
      <h3 class="text-lg font-semibold line-clamp-1">{{ test.title }}</h3>
      <span 
        v-if="test.enrollment_status === 'creator'"
        class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded"
      >
        Created by you
      </span>
      <span 
        v-else-if="test.enrollment_status === 'enrolled'"
        class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded"
      >
        Enrolled
      </span>
    </div>

    <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ test.description }}</p>
    
    <div class="flex justify-between items-center text-sm text-gray-500">
      <div>
        <span v-if="test.is_public" class="text-purple-600">Public</span>
        <span v-else class="text-gray-600">Private</span>
        • {{ test.question_count }} questions
      </div>
      <div>Created {{ formattedDate }}</div>
    </div>
  </div>
</template>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>