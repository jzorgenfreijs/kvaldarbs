<script setup>
import { ref } from 'vue';

const props = defineProps({
  questionText: String,
});

const emit = defineEmits(['update:questionText']);
const answer = ref('');

const handleQuestionTextChange = (event) => {
  emit('update:questionText', event.target.innerText);
};
</script>

<template>
  <div class="bg-gray-300 rounded p-4">
    <div
      contenteditable="true"
      @blur="handleQuestionTextChange"
      @dblclick="(event) => event.target.focus()"
      class="mb-2 cursor-pointer"
    >
      {{ questionText || 'Text Answer Question:' }}
    </div>
    
    <v-textarea
      v-model="answer"
      rows="3"
      auto-grow
    ></v-textarea>
  </div>
</template>