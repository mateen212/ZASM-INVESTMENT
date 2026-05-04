import { createApp } from 'vue'
import fundingInfo from './components/fundingInfo/plaid.vue'
import adminACHPayment from './components/fundingInfo/adminACH.vue'
import Counter from './components/Counter.vue'
import Loading from './components/Loading.vue'


const userDocument = createApp()

userDocument.component('funding-info', fundingInfo)
userDocument.component('admin-ach-payment', adminACHPayment)
userDocument.component('counter', Counter)
userDocument.component('loading', Loading)
userDocument.mount('#v-funding-info')