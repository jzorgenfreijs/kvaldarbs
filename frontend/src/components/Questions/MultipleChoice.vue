<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  questionText: String,
  options: Array,
  correctAnswer: Array
});

const emit = defineEmits(['update:questionText', 'update:options', 'update:correctAnswer']);
const selectedCorrectIndices = ref(props.correctAnswer || []);

const handleQuestionTextChange = (event) => {
  emit('update:questionText', event.target.innerText);
};

const handleOptionTextChange = (index, event) => {
  const updatedOptions = [...props.options];
  updatedOptions[index] = event.target.innerText;
  emit('update:options', updatedOptions);
};

const updateCorrectAnswers = (index) => {
  const currentIndex = selectedCorrectIndices.value.indexOf(index);
  
  if (currentIndex === -1) {
    selectedCorrectIndices.value = [...selectedCorrectIndices.value, index];
  } else {
    selectedCorrectIndices.value = selectedCorrectIndices.value.filter(i => i !== index);
  }
  
  emit('update:correctAnswer', [...selectedCorrectIndices.value]);
};


watch(() => props.correctAnswer, (newVal) => {
  if (JSON.stringify(newVal) !== JSON.stringify(selectedCorrectIndices.value)) {
    selectedCorrectIndices.value = newVal ? [...newVal] : [];
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
      {{ questionText || 'Multiple Choice Question:' }}
    </div>
    
    <div class="pl-2">
      <v-checkbox
        v-for="(option, index) in options"
        :key="index"
        :model-value="selectedCorrectIndices.includes(index)"
        @update:model-value="updateCorrectAnswers(index); console.log(selectedCorrectIndices)"
        density="compact"
      >
        <template v-slot:label>
          <div
            contenteditable="true"
            @blur="(event) => handleOptionTextChange(index, event)"
            @dblclick="(event) => event.target.focus()"
            class="cursor-pointer"
          >
            {{ option || `Choice ${index + 1}` }}
          </div>
        </template>
      </v-checkbox>
    </div>
  </div>
</template>