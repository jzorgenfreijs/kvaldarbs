<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  questionText: String,
  correctAnswer: [String, Array] // Flexible prop for compatibility
});

const emit = defineEmits(['update:correctAnswer']);
const answer = ref(
  typeof props.correctAnswer === 'string' 
    ? props.correctAnswer 
    : '' // Default empty string if array or undefined
);

watch(answer, (newValue) => {
  emit('update:correctAnswer', newValue); // Emit raw string
});
</script>

<template>
  <div class="bg-gray-50 rounded p-4">
    <h3 class="text-lg font-medium mb-2">{{ questionText }}</h3>
    
    <v-textarea
      v-model="answer"
      rows="3"
      auto-grow
      outlined
      placeholder="Type your answer here..."
    ></v-textarea>
  </div>
</template>