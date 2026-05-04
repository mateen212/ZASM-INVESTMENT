import { createApp } from 'vue'
import Counter from './components/Counter.vue'
import HurdleComponent from './components/HurdleComponent.vue'
import Waterfall from './components/Waterfall.vue'
import Gp_Provision from './components/Gp_Provision.vue'
import Loading from './components/Loading.vue'
import StopConditionTemplate from './components/StopConditionTemplate.vue'

const waterfall = createApp()

waterfall.component('counter', Counter)
waterfall.component('loading', Loading)
waterfall.component('hurdle-component', HurdleComponent)
waterfall.component('waterfall-component', Waterfall)
waterfall.component('gp-provision', Gp_Provision)
waterfall.component('stop-condition-template', StopConditionTemplate)
waterfall.mount('#v-waterfall')