import { createRouter, createWebHistory } from "vue-router";
import invoiceIndex from '../components/invoices/index.vue'
import notFound from '../components/NotFound.vue'

const routes = [
    {
        path: '/',
        component: invoiceIndex
    },
    {
        path: '/:patchMatch(.*)*',
        component: notFound
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router
