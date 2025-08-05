// import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import Index from './Index.vue'
import router from './router'

const app = createApp(Index)

app.use(router)

app.mount('#app')
