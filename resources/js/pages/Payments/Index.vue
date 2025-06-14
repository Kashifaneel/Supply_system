<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Plus, Eye, Check, X } from 'lucide-vue-next';

interface Payment {
    id: number;
    cheque_no: string;
    amount: number;
    status: 'Pending' | 'Confirmed';
    cheque_image?: string;
    created_at: string;
    supply: {
        id: number;
        supply_date: string;
        purchase_order: {
            po_number: string;
            institution_name: string;
        };
        user: {
            name: string;
        };
    };
}

interface Props {
    payments: {
        data: Payment[];
        links: any[];
        meta: any;
    };
}

defineProps<Props>();

const page = usePage();
const user = page.props.auth?.user;

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Payments', href: '/payments' },
];

const getStatusColor = (status: string) => {
    switch (status) {
        case 'Pending':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
        case 'Confirmed':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
    }
};

const confirmPayment = (id: number) => {
    if (confirm('Are you sure you want to confirm this payment?')) {
        router.post(route('payments.confirm', id));
    }
};

const rejectPayment = (id: number) => {
    if (confirm('Are you sure you want to reject this payment? This action cannot be undone.')) {
        router.delete(route('payments.reject', id));
    }
};
</script>

<template>
    <Head title="Payments" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Payments</h1>
                    <p class="text-muted-foreground">
                        Manage payment submissions and confirmations
                    </p>
                </div>
                <Link :href="route('payments.create')">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Submit Payment
                    </Button>
                </Link>
            </div>

            <div class="grid gap-4">
                <Card v-for="payment in payments.data" :key="payment.id">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle class="text-lg">Payment #{{ payment.id }}</CardTitle>
                                <CardDescription>
                                    {{ payment.supply.purchase_order.po_number }} • {{ payment.supply.purchase_order.institution_name }}
                                </CardDescription>
                            </div>
                            <div class="flex items-center gap-2">
                                <Badge :class="getStatusColor(payment.status)">
                                    {{ payment.status }}
                                </Badge>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="space-y-2">
                                <p class="text-sm text-muted-foreground">Cheque Details</p>
                                <p><strong>Cheque No:</strong> {{ payment.cheque_no }}</p>
                                <p><strong>Amount:</strong> PKR {{ payment.amount.toLocaleString() }}</p>
                                <p><strong>Submitted:</strong> {{ new Date(payment.created_at).toLocaleDateString() }}</p>
                            </div>
                            <div class="space-y-2">
                                <p class="text-sm text-muted-foreground">Supply Details</p>
                                <p><strong>Supply Date:</strong> {{ new Date(payment.supply.supply_date).toLocaleDateString() }}</p>
                                <p><strong>Supplied By:</strong> {{ payment.supply.user.name }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <p v-if="payment.cheque_image" class="text-sm text-muted-foreground">
                                    <a :href="`/storage/${payment.cheque_image}`" target="_blank" class="text-blue-600 hover:underline">
                                        View Cheque Image
                                    </a>
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <Link :href="route('payments.show', payment.id)">
                                    <Button variant="outline" size="sm">
                                        <Eye class="h-4 w-4" />
                                    </Button>
                                </Link>
                                
                                <template v-if="user?.role === 'Admin' && payment.status === 'Pending'">
                                    <Button variant="outline" size="sm" @click="confirmPayment(payment.id)" class="text-green-600 hover:text-green-700">
                                        <Check class="h-4 w-4" />
                                    </Button>
                                    <Button variant="outline" size="sm" @click="rejectPayment(payment.id)" class="text-red-600 hover:text-red-700">
                                        <X class="h-4 w-4" />
                                    </Button>
                                </template>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div v-if="payments.data.length === 0" class="text-center py-12">
                    <p class="text-muted-foreground">No payments found.</p>
                    <Link :href="route('payments.create')" class="mt-4">
                        <Button>Submit your first payment</Button>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
