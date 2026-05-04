import { createApp } from 'vue'
import RetrieveTemplate from './components/user_template/RetrieveTemplate.vue'
import Counter from './components/Counter.vue'
import Loading from './components/Loading.vue'


const userDocument = createApp()

userDocument.component('retrieve-template', RetrieveTemplate)
userDocument.component('counter', Counter)
userDocument.component('loading', Loading)
userDocument.mount('#v-user-document')