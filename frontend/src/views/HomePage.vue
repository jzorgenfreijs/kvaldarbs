<script setup>
import HeaderBar from '../components/HeaderBar.vue';

import { useAuthStore } from '../stores/auth';
import { ref, onMounted} from 'vue'
import { useRouter } from 'vue-router';

const router = useRouter();
const authStore = useAuthStore();

onMounted(async () => {
    await authStore.getUser();
});
</script>

<template>
<div class="sticky top-0 z-50 shadow-md">
    <HeaderBar/>
</div>
<div class="bg-white cursor-default">
    <v-parallax src="https://api.jzorgenfreijs.com/storage/frontend-pics/BG_pattern_2.svg" class="flex align-center">
        <div class="relative isolate px-6 pt-14 lg:px-8">
            <div class="mx-auto max-w-2xl py-16 text-center sm:py-48 lg:py-56 bg-white shadow-md">
                <h1 class="text-heading-1 font-heading tracking-tight text-gray-900">
                    Take Online Tests with Ease
                </h1>
                <p class="mt-6 text-lg leading-8 text-gray-600">
                    Access a wide range of online tests and quizzes designed for students, professionals, and educators.
                </p>
                <div class="flex gap-4 mt-4 w-full justify-center">
                    <v-btn color="#6557ff" @click="router.push('/login')">Get Started</v-btn>
                </div>
            </div>
    </div>
    </v-parallax>
</div>

<section class="mx-auto mt-16 flex flex-col lg:flex-row items-center justify-between max-w-7xl px-6">
    <div class="w-full mb-8 lg:w-1/2 lg:pr-8 lg:mb-0">
        <h1 class="text-3xl font-bold mb-4">A Seamless Testing Experience</h1>
        <p class="text-lg">
            Our platform provides an easy-to-use interface for taking quizzes, exams, and practice tests. Whether you're preparing for school, certification, or professional assessments, we have the tools to support you.
        </p>
    </div>
    <v-lazy :options="{'threshold':0.5}" transition="fade-transition" class="w-full lg:w-1/2 lg:pl-8">
        <div>
            <!-- <v-skeleton-loader type="image"></v-skeleton-loader> -->
             <v-img
                src="https://api.jzorgenfreijs.com/storage/frontend-pics/ofspace-llc-ABdQMHYg3ww-unsplash.jpg"
            ></v-img>
            <!-- Photo by Ofspace LLC on Unsplash - https://unsplash.com/photos/person-holding-a-silver-laptop-computer-ABdQMHYg3ww -->
            Photo by <a href="https://unsplash.com/@ofspace?utm_content=creditCopyText&utm_medium=referral&utm_source=unsplash">Ofspace LLC</a> on <a href="https://unsplash.com/photos/person-holding-a-silver-laptop-computer-ABdQMHYg3ww?utm_content=creditCopyText&utm_medium=referral&utm_source=unsplash">Unsplash</a>
      
        </div>
    </v-lazy>
</section>

<section class="mx-auto mt-10 flex flex-col-reverse items-center justify-between max-w-7xl px-6 mb-40 lg:flex-row">
    <v-lazy :options="{'threshold':0.5}" transition="fade-transition" class="w-full mt-8 lg:w-1/2 lg:pr-8 lg:mt-0">
        <div>
            <!-- <v-skeleton-loader type="image"></v-skeleton-loader> -->
            <v-img
                src="https://api.jzorgenfreijs.com/storage/frontend-pics/john-FlPc9_VocJ4-unsplash.jpg"
            ></v-img>
            <!-- Photo by Josh on Unsplash - https://unsplash.com/photos/person-using-laptop-FlPc9_VocJ4 -->
            Photo by <a href="https://unsplash.com/@johnishappysometimes?utm_content=creditCopyText&utm_medium=referral&utm_source=unsplash">John</a> on <a href="https://unsplash.com/photos/person-using-laptop-FlPc9_VocJ4?utm_content=creditCopyText&utm_medium=referral&utm_source=unsplash">Unsplash</a>
        </div>
    </v-lazy>
    <div class="w-full lg:w-1/2 lg:pl-8">
        <h1 class="text-3xl font-bold mb-4">Advanced Test Management</h1>
        <p class="text-lg">
            Customize your test experience with various difficulty levels and question formats. Track your progress and get instant feedback to improve your performance.
        </p>
    </div>
</section>
<div class="h-4">
</div>
</template>
