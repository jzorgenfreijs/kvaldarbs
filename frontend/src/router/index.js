import { createWebHistory, createRouter } from "vue-router";
import Home from "../views/HomePage.vue"
import Login from "../views/Login.vue"
import Register from "../views/Register.vue"
import Profile from "../views/Profile.vue";

const routes = [
  {
    path: "/",
    name: "Home",
    component: Home,
  },
  {
    path: "/login",
    name: "Login",
    component: Login,
  },
  {
    path: "/register",
    name: "Register",
    component: Register,
  },
  {
    path: "/profile",
    name: "Profile",
    component: Profile,
  },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});
  
export default router;