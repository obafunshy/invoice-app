import { createRouter, createWebHistory } from "vue-router";
import invoiceIndex from '../components/invoices/index.vue'
import invoiceNew from '../components/invoices/new.vue'
import invoiceShow from '../components/invoices/show.vue'
import invoiceEdit from '../components/invoices/edit.vue'
import notFound from '../components/NotFound.vue'

const routes = [
    {
        path: '/',
        component: () => import('../components/invoices/index.vue')
    },
    {
        path: '/invoice/new',
        component: () => import('../components/invoices/new.vue')
    },
    {
        path: '/invoice/show/:id',
        component: () => import('../components/invoices/show.vue'),
        props: true
    },
    {
        path: '/invoice/edit/:id',
        component: () => import('../components/invoices/edit.vue'),
        props: true
    },
    {
        path: '/:patchMatch(.*)*',
        component: () => import('../components/NotFound.vue')
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router
