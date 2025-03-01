<script setup>
import { ref } from 'vue';
import HeaderButtons from './HeaderButtons.vue';

const isOpen = ref(false);
</script>

<template>
  <header>
    <nav>
      <div class="flex justify-between h-16 px-10 shadow items-center bg-white">
        <div class="flex items-center space-x-8">
          <router-link to="/" class="text-xl font-bold cursor-pointer text-black">
            Classify
          </router-link>

          <div class="hidden justify-around space-x-4 md:flex">
            <router-link class="relative text-gray-700 link-underline" to="/dashboard">Home</router-link>
            <router-link class="relative text-gray-700 link-underline" to="/my-courses">My Courses</router-link>
          </div>
        </div>

        <div class="hidden md:flex">
          <HeaderButtons />
        </div>

        <div class="md:hidden">
          <button @click="isOpen = !isOpen" class="text-gray-800 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
          </button>
        </div>
      </div>

      <div v-if="isOpen" class="bg-white shadow-lg px-6 py-4 space-y-2 md:hidden">
        <router-link class="block text-gray-700 link-underline" to="/">Home</router-link>
        <router-link class="block text-gray-700 link-underline" to="/">My Courses</router-link>

        <div class="border-t mt-2 pt-2">
          <HeaderButtons class="px-1" />
        </div>
      </div>
    </nav>
  </header>
</template>

<style scoped>
.link-underline::before {
  content: "";
  position: absolute;
  width: 0;
  height: 3px;
  bottom: -2px;
  left: 0;
  background-color: black;
  visibility: hidden;
  transition: all 0.5s ease-in-out;
}

.link-underline:hover::before {
  visibility: visible;
  width: 100%;
}
</style>