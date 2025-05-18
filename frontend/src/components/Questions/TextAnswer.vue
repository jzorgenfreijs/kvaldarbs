<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  questionText: String,
  correctAnswer: [String, Array]
});

const emit = defineEmits(['update:questionText', 'update:correctAnswer']);
const answer = ref(
  typeof props.correctAnswer === 'string' 
    ? props.correctAnswer 
    : ''
);

const handleQuestionTextChange = (event) => {
  emit('update:questionText', event.target.innerText);
};

watch(answer, (newValue) => {
  emit('update:correctAnswer', newValue);
});
</script>

<template>
  <div class="bg-gray-300 rounded p-4">
    <div
      contenteditable="true"
      @blur="handleQuestionTextChange"
      @dblclick="(event) => event.target.focus()"
      class="mb-2 cursor-pointer"
    >
      {{ questionText || 'Text Answer Question' }}
    </div>
    
    <v-textarea
      v-model="answer"
      rows="3"
      auto-grow
      outlined
      placeholder="Type your answer here..."
    ></v-textarea>
  </div>
</template>

<style scoped>
[contenteditable="true"] {
  min-height: 1.5em;
  padding: 2px;
  outline: none;
}

[contenteditable="true"]:focus {
  background-color: #f0f0f0;
  border-radius: 4px;
}
</style>