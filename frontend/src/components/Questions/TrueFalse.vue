<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  questionText: String,
  correctAnswer: Array
});

const emit = defineEmits(['update:questionText', 'update:correctAnswer']);
const selectedCorrectIndex = ref(props.correctAnswer?.[0] ?? 0);

const handleQuestionTextChange = (event) => {
  emit('update:questionText', event.target.innerText);
};

const updateCorrectAnswer = (value) => {
  selectedCorrectIndex.value = value === 'true' ? 0 : 1;
  emit('update:correctAnswer', [selectedCorrectIndex.value]);
};

watch(() => props.correctAnswer, (newVal) => {
  if (newVal?.[0] !== selectedCorrectIndex.value) {
    selectedCorrectIndex.value = newVal?.[0] ?? 0;
  }
}, { deep: true });
</script>

<template>
  <div class="bg-gray-300 rounded p-4">
    <div
      contenteditable="true"
      @blur="handleQuestionTextChange"
      @dblclick="(event) => event.target.focus()"
      class="mb-2 cursor-pointer"
    >
      {{ questionText || 'True/False Question:' }}
    </div>
    
    <v-radio-group 
      :model-value="selectedCorrectIndex === 0 ? 'true' : 'false'"
      @update:model-value="updateCorrectAnswer"
    >
      <v-radio label="True" value="true"></v-radio>
      <v-radio label="False" value="false"></v-radio>
    </v-radio-group>
  </div>
</template>