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

const updateCorrectAnswer = (index) => {
  selectedCorrectIndex.value = index;
  emit('update:correctAnswer', [index]);
};

watch(() => props.correctAnswer, (newVal) => {
  if (newVal?.[0] !== selectedCorrectIndex.value) {
    selectedCorrectIndex.value = newVal?.[0] ?? null;
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
            {{ questionText }}
        </div>
  
        <v-radio-group 
          :model-value="selectedCorrectIndex"
          @update:model-value="updateCorrectAnswer"
        >
            <v-radio
            v-for="(option, index) in options"
            :key="index"
            :value="option"
            >
                <template v-slot:label>
                    <div
                        contenteditable="true"
                        @blur="(event) => handleOptionTextChange(index, event)"
                        @dblclick="(event) => event.target.focus()"
                        class="cursor-pointer"
                    >
                        {{ option }}
                    </div>
                </template>
            </v-radio>
        </v-radio-group>
    </div>
</template>