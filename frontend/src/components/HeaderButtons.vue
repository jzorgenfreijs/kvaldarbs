<script setup>
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

function showDropdown() {
  document.getElementById("Dropdown").classList.toggle("show");
}

window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}

</script>

<template>
    <template v-if="!authStore.user">
        <v-btn
        color="#6557ff"
        @click="router.push('/login')"
        >Login</v-btn>
    </template>
    <template v-else>
        <v-menu :offset="[7, 44]">
            <template v-slot:activator="{ props }">
                <v-img
                :width="50"
                :height="50"
                aspect-ratio="1/1"
                src="https://api.jzorgenfreijs.com/storage/frontend-pics/picture-placeholder.png"
                v-bind="props"
                class="rounded-xl hover:bg-gray-200"
                ></v-img>
            </template>
            <v-list>
                <v-list-item>
                  <v-btn
                  color="#6557ff"
                  :width="125"
                  @click="router.push('/profile')"
                  >Profile</v-btn>
                </v-list-item>
                <v-list-item>
                  <v-btn
                  color="#6557ff"
                  :width="125"
                  @click="authStore.handleLogout()"
                  >Logout</v-btn>
                </v-list-item>
            </v-list>
        </v-menu>
    </template>
</template>