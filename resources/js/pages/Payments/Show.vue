<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Eye, Check, X, Download } from 'lucide-vue-next';

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
        dc_pdf?: string;
        invoice_pdf?: string;
        total_amount: number;
        purchase_order: {
            id: number;
            po_number: string;
            institution_name: string;
            institution_email?: string;
            institution_phone?: string;
            institution_address?: string;
        };
        user: {
            name: string;
            email: string;
        };
        items: Array<{
            id: number;
            quantity: number;
            po_item: {
                id: number;
                name: string;
                price: number;
                batch_no?: string;
                mfg_date?: string;
                exp_date?: string;
            };
        }>;
    };
}

interface Props {
    payment: Payment;
}

defineProps<Props>();

const page = usePage();
const user = page.props.auth?.user;

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Payments', href: '/payments' },
    { title: 'View', href: '#' },
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

const confirmPayment = () => {
    if (confirm('Are you sure you want to confirm this payment?')) {
        router.post(route('payments.confirm', payment.id));
    }
};

const rejectPayment = () => {
    if (confirm('Are you sure you want to reject this payment? This action cannot be undone.')) {
        router.delete(route('payments.reject', payment.id));
    }
};
</script>

<template>
    <Head :title="`Payment #${payment.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Payment #{{ payment.id }}</h1>
                    <p class="text-muted-foreground">
                        Payment Details and Information
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Badge :class="getStatusColor(payment.status)">
                        {{ payment.status }}
                    </Badge>
                    <template v-if="user?.role === 'Admin' && payment.status === 'Pending'">
                        <Button variant="outline" @click="confirmPayment" class="text-green-600 hover:text-green-700">
                            <Check class="mr-2 h-4 w-4" />
                            Confirm
                        </Button>
                        <Button variant="outline" @click="rejectPayment" class="text-red-600 hover:text-red-700">
                            <X class="mr-2 h-4 w-4" />
                            Reject
                        </Button>
                    </template>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <!-- Payment Details -->
                <Card>
                    <CardHeader>
                        <CardTitle>Payment Information</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Payment ID</p>
                            <p class="text-lg font-semibold">#{{ payment.id }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Cheque Number</p>
                            <p class="font-semibold">{{ payment.cheque_no }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Amount</p>
                            <p class="text-lg font-semibold">PKR {{ Number(payment.amount).toLocaleString() }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Status</p>
                            <Badge :class="getStatusColor(payment.status)">
                                {{ payment.status }}
                            </Badge>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Submitted Date</p>
                            <p>{{ new Date(payment.created_at).toLocaleDateString() }}</p>
                        </div>
                        <div v-if="payment.cheque_image">
                            <p class="text-sm font-medium text-muted-foreground">Cheque Image</p>
                            <a :href="`/storage/${payment.cheque_image}`" target="_blank" class="text-blue-600 hover:underline">
                                View Cheque Image
                            </a>
                        </div>
                    </CardContent>
                </Card>

                <!-- Supply Details -->
                <Card>
                    <CardHeader>
                        <CardTitle>Supply Information</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Supply ID</p>
                            <p class="font-semibold">#{{ payment.supply.id }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Supply Date</p>
                            <p>{{ new Date(payment.supply.supply_date).toLocaleDateString() }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Supplied By</p>
                            <p>{{ payment.supply.user.name }}</p>
                            <p class="text-sm text-muted-foreground">{{ payment.supply.user.email }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Supply Amount</p>
                            <p class="text-lg font-semibold">PKR {{ Number(payment.supply.total_amount).toLocaleString() }}</p>
                        </div>
                        <div class="flex gap-2">
                            <Link :href="route('supplies.show', payment.supply.id)">
                                <Button variant="outline" size="sm">
                                    <Eye class="mr-2 h-4 w-4" />
                                    View Supply
                                </Button>
                            </Link>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Purchase Order Details -->
            <Card>
                <CardHeader>
                    <CardTitle>Purchase Order Information</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">PO Number</p>
                            <p class="font-semibold">{{ payment.supply.purchase_order.po_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Institution</p>
                            <p>{{ payment.supply.purchase_order.institution_name }}</p>
                        </div>
                        <div v-if="payment.supply.purchase_order.institution_email">
                            <p class="text-sm font-medium text-muted-foreground">Email</p>
                            <p>{{ payment.supply.purchase_order.institution_email }}</p>
                        </div>
                        <div v-if="payment.supply.purchase_order.institution_phone">
                            <p class="text-sm font-medium text-muted-foreground">Phone</p>
                            <p>{{ payment.supply.purchase_order.institution_phone }}</p>
                        </div>
                    </div>
                    <div v-if="payment.supply.purchase_order.institution_address">
                        <p class="text-sm font-medium text-muted-foreground">Address</p>
                        <p class="whitespace-pre-line">{{ payment.supply.purchase_order.institution_address }}</p>
                    </div>
                    <div class="flex gap-2">
                        <Link :href="route('purchase-orders.show', payment.supply.purchase_order.id)">
                            <Button variant="outline" size="sm">
                                <Eye class="mr-2 h-4 w-4" />
                                View PO
                            </Button>
                        </Link>
                    </div>
                </CardContent>
            </Card>

            <!-- Items Supplied -->
            <Card>
                <CardHeader>
                    <CardTitle>Items in Supply</CardTitle>
                    <CardDescription>
                        Items included in the supply for this payment
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left p-2">Item Name</th>
                                    <th class="text-left p-2">Batch No</th>
                                    <th class="text-left p-2">Mfg Date</th>
                                    <th class="text-left p-2">Exp Date</th>
                                    <th class="text-right p-2">Price</th>
                                    <th class="text-right p-2">Quantity</th>
                                    <th class="text-right p-2">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in payment.supply.items" :key="item.id" class="border-b">
                                    <td class="p-2 font-medium">{{ item.po_item.name }}</td>
                                    <td class="p-2">{{ item.po_item.batch_no || '-' }}</td>
                                    <td class="p-2">{{ item.po_item.mfg_date ? new Date(item.po_item.mfg_date).toLocaleDateString() : '-' }}</td>
                                    <td class="p-2">{{ item.po_item.exp_date ? new Date(item.po_item.exp_date).toLocaleDateString() : '-' }}</td>
                                    <td class="p-2 text-right">PKR {{ Number(item.po_item.price).toLocaleString() }}</td>
                                    <td class="p-2 text-right">{{ item.quantity }}</td>
                                    <td class="p-2 text-right font-semibold">PKR {{ Number(item.quantity * item.po_item.price).toLocaleString() }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            <!-- Documents -->
            <Card v-if="payment.supply.dc_pdf || payment.supply.invoice_pdf">
                <CardHeader>
                    <CardTitle>Related Documents</CardTitle>
                    <CardDescription>
                        Documents related to this supply and payment
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-2">
                        <a v-if="payment.supply.dc_pdf" :href="`/storage/${payment.supply.dc_pdf}`" target="_blank">
                            <Button variant="outline" size="sm">
                                <Download class="mr-2 h-4 w-4" />
                                Download DC
                            </Button>
                        </a>
                        <a v-if="payment.supply.invoice_pdf" :href="`/storage/${payment.supply.invoice_pdf}`" target="_blank">
                            <Button variant="outline" size="sm">
                                <Download class="mr-2 h-4 w-4" />
                                Download Invoice
                            </Button>
                        </a>
                    </div>
                </CardContent>
            </Card>

            <!-- Payment Summary -->
            <Card>
                <CardHeader>
                    <CardTitle>Payment Summary</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="bg-muted/50 rounded-lg p-4">
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span>Supply Amount:</span>
                                <span>PKR {{ Number(payment.supply.total_amount).toLocaleString() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Payment Amount:</span>
                                <span>PKR {{ Number(payment.amount).toLocaleString() }}</span>
                            </div>
                            <div class="flex justify-between font-medium pt-2 border-t">
                                <span>Payment Status:</span>
                                <Badge :class="getStatusColor(payment.status)">
                                    {{ payment.status }}
                                </Badge>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Actions -->
            <div class="flex justify-end gap-4">
                <Link :href="route('payments.index')">
                    <Button variant="outline">
                        Back to Payments
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
