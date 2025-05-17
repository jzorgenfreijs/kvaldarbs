<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  questionText: String,
  options: Array,
  correctAnswer: Array
});

const emit = defineEmits(['update:questionText', 'update:options', 'update:correctAnswer']);
const selectedCorrectIndex = ref(props.correctAnswer?.[0] ?? null);

const handleQuestionTextChange = (event) => {
  emit('update:questionText', event.target.innerText);
};

const handleOptionTextChange = (index, event) => {
  const updatedOptions = [...props.options];
  updatedOptions[index] = event.target.innerText;
  emit('update:options', updatedOptions);
};

const updateCorrectAnswer = (selectedOptionText) => {
  const selectedIndex = props.options.findIndex(opt => opt === selectedOptionText);
  selectedCorrectIndex.value = selectedIndex;
  emit('update:correctAnswer', [selectedIndex])
};

// Sync with external changes to correctAnswer
watch(() => props.correctAnswer, (newVal) => {
  selectedCorrectIndex.value = newVal?.[0] ?? null;
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
      {{ questionText || 'Single Choice Question' }}
    </div>
    
    <v-radio-group 
      :model-value="selectedCorrectIndex !== null ? options[selectedCorrectIndex] : null"
      @update:model-value="updateCorrectAnswer"
    >
      <v-radio
        v-for="(option, index) in options"
        :key="index"
        :value="option"
        class="mb-2"
      >
        <template v-slot:label>
          <div
            contenteditable="true"
            @blur="(event) => handleOptionTextChange(index, event)"
            @dblclick="(event) => event.target.focus()"
            class="cursor-pointer"
          >
            {{ option || `Option ${index + 1}` }}
          </div>
        </template>
      </v-radio>
    </v-radio-group>
  </div>
</template>

<style scoped>
.v-radio-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
</style>