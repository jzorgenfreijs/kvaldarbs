import { createWebHistory, createRouter } from "vue-router";
import Home from "../views/HomePage.vue"
import Login from "../views/Login.vue"
import Register from "../views/Register.vue"
import ForgotPassword from "../views/ForgotPassword.vue";
import ResetPassword from "../views/ResetPassword.vue";
import Profile from "../views/Profile.vue";
import MyTests from "../views/MyTests.vue";
import CreateTest from "../views/CreateTest.vue";
import TestList from "../views/TestList.vue";
import TakeTest from "../views/TakeTest.vue";
import GradeTest from "../views/GradeTest.vue";
import ChangePassword from "../views/ChangePassword.vue";

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
    path: "/forgot-password",
    name: "ForgotPass",
    component: ForgotPassword,
  },
  {
    path: "/password-reset/:token",
    name: "password-reset",
    component: ResetPassword,
  },
  {
    path: "/change-password",
    name: "change-password",
    component: ChangePassword,
  },
  {
    path: "/profile",
    name: "Profile",
    component: Profile,
  },
  {
    path: "/my-tests",
    name: "My Tests",
    component: MyTests,
  },
  {
    path: "/create-test",
    name: "Create Test",
    component: CreateTest,
  },
  {
    path: "/tests",
    name: "Tests",
    component: TestList,
  },
  {
    path: '/test/:id',
    name: 'TakeTest',
    component: TakeTest,
  },
  {
    path: '/test/:id/grade',
    name: 'GradeTest',
    component: GradeTest,
  },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});
  
export default router;