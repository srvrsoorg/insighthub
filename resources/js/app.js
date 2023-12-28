import './bootstrap';

import {createApp, defineAsyncComponent} from 'vue'
import App from './App.vue'
import Router from '@/router'
import { createPinia } from 'pinia'
import piniaPersist from 'pinia-plugin-persist'
import Toast, { POSITION } from "vue-toastification";
import VueDatePicker from '@vuepic/vue-datepicker';
import VueApexCharts from "vue3-apexcharts";
import VTooltip from 'v-tooltip'
import axios from '@/plugins/axios'

import $ from "jquery";
window.$ = window.jQuery = $;

// Import mixins globally
import ErrorMixin from '@/mixins/validationError'
import GlobalMixin from '@/mixins/global'
import VerifyApi from '@/mixins/verifyApi'
import color from '@/mixins/color'

const Badge = defineAsyncComponent(() => import('@/components/uiElements/Badge.vue'))
const CustomScrollbar = defineAsyncComponent(() => import('@/components/uiElements/CustomScrollbar.vue'))
const Button = defineAsyncComponent(() => import('@/components/uiElements/Button.vue'))
const Confirmation = defineAsyncComponent(() => import('@/components/uiElements/ConfirmationModal.vue'))
const Modal = defineAsyncComponent(() => import('@/components/uiElements/Modal.vue'))
const PasswordVisibility = defineAsyncComponent(() => import('@/components/uiElements/PasswordVisibility.vue'))
const Skeleton = defineAsyncComponent(() => import('@/components/uiElements/Skeleton.vue'))
const Table = defineAsyncComponent(() => import('@/components/uiElements/Table.vue'))
const TableTitle = defineAsyncComponent(() => import('@/components/uiElements/TableTitle.vue'))
const TableSkeleton = defineAsyncComponent(() => import('@/components/uiElements/TableSkeleton.vue'))
const BackToLink = defineAsyncComponent(() => import('@/components/uiElements/BackToLink.vue'))
const Pagination = defineAsyncComponent(() => import('@/components/uiElements/Pagination.vue'))
const PerPage = defineAsyncComponent(() => import('@/components/uiElements/PerPage.vue'))
const SectionHeader = defineAsyncComponent(() => import('@/components/uiElements/SectionHeader.vue'))
const ServerOverview = defineAsyncComponent(() => import('@/components/user/ServerOverview.vue'))
const DateFilter = defineAsyncComponent(() => import('@/components/filter/DateFilter.vue'))
const BotSwitch = defineAsyncComponent(() => import('@/components/filter/BotSwitch.vue'))

// Create Pinia store
const pinia = createPinia()
pinia.use(piniaPersist)

// Create Vue app
const app = createApp(App)

app.mixin(ErrorMixin)
app.mixin(GlobalMixin)
app.mixin(VerifyApi)
app.mixin(color)

// Use Pinia for state management
app.use(pinia)

app.use(Router)

import debounce from 'lodash/debounce';
app.config.globalProperties.$debounce = debounce;

// Use Toastification for toast messages
import toast from '@/plugins/toast-notification'
app.config.globalProperties.$toast = toast
app.use(Toast, {
    position: POSITION.TOP_RIGHT
})
app.config.globalProperties.$toast = toast

// Use Apexcharts for charts
app.use(VueApexCharts);

//Use VTooltip for tooltips
app.use(VTooltip, {
    defaultPlacement:'auto',
    disposeTimeout: 5000,
})

// Configure axios
app.config.globalProperties.$axios = axios

// Register custom components globally
app.component('Badge', Badge)
app.component('Button', Button)
app.component('CustomScrollbar', CustomScrollbar)
app.component('VueDatePicker', VueDatePicker)
app.component('Pagination', Pagination)
app.component('CustomScrollbar', CustomScrollbar)
app.component('Modal', Modal)
app.component('Confirmation', Confirmation)
app.component('PasswordVisibility', PasswordVisibility)
app.component('Skeleton', Skeleton)
app.component('Table', Table)
app.component('TableTitle', TableTitle)
app.component('TableSkeleton', TableSkeleton)
app.component('BackToLink', BackToLink),
app.component('PerPage', PerPage),
app.component('ServerOverview', ServerOverview),
app.component('DateFilter', DateFilter),
app.component('BotSwitch', BotSwitch)
app.component('SectionHeader', SectionHeader)

app.mount("#app")