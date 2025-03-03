import { createApp } from 'vue'
import { createPinia } from 'pinia'
import './style.css'
import router from './router'

import './axios'

import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

import App from './App.vue'

const vuetify = createVuetify({
    components,
    directives,
})

const pinia = createPinia();

createApp(App).use(pinia).use(vuetify).use(router).mount('#app')
