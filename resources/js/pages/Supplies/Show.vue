<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Download, Upload, CreditCard, Eye } from 'lucide-vue-next';
import { ref } from 'vue';

interface Supply {
    id: number;
    supply_date: string;
    dc_pdf?: string;
    invoice_pdf?: string;
    dc_stamped?: string;
    invoice_stamped?: string;
    total_amount: number;
    purchase_order: {
        id: number;
        po_number: string;
        institution_name: string;
        status: string;
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
    payments: Array<{
        id: number;
        cheque_no: string;
        amount: number;
        status: 'Pending' | 'Confirmed';
        created_at: string;
    }>;
}

interface Props {
    supply: Supply;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Supplies', href: '/supplies' },
    { title: 'View', href: '#' },
];

const uploadForm = useForm({
    dc_stamped: null as File | null,
    invoice_stamped: null as File | null,
});

const showUploadForm = ref(false);



const submitUpload = () => {
    uploadForm.post(route('supplies.upload-stamped', props.supply.id), {
        onSuccess: () => {
            showUploadForm.value = false;
            uploadForm.reset();
        },
        preserveScroll: true
    });
};

const getPaymentStatusColor = (status: string) => {
    switch (status) {
        case 'Pending':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
        case 'Confirmed':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
    }
};

const hasConfirmedPayment = (supply: Supply) => {
    return supply.payments.some(payment => payment.status === 'Confirmed');
};
</script>

<template>
    <Head :title="`Supply #${supply.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Supply #{{ supply.id }}</h1>
                    <p class="text-muted-foreground">
                        Supply Details and Documents
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Link :href="route('purchase-orders.show', supply.purchase_order.id)">
                        <Button variant="outline">
                            <Eye class="mr-2 h-4 w-4" />
                            View PO
                        </Button>
                    </Link>
                    <Link v-if="!hasConfirmedPayment(supply)" :href="route('payments.create', { supply: supply.id })">
                        <Button>
                            <CreditCard class="mr-2 h-4 w-4" />
                            Create Payment
                        </Button>
                    </Link>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <!-- Supply Details -->
                <Card>
                    <CardHeader>
                        <CardTitle>Supply Information</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Supply ID</p>
                            <p class="text-lg font-semibold">#{{ supply.id }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Supply Date</p>
                            <p>{{ new Date(supply.supply_date).toLocaleDateString() }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Supplied By</p>
                            <p>{{ supply.user.name }}</p>
                            <p class="text-sm text-muted-foreground">{{ supply.user.email }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Total Amount</p>
                            <p class="text-lg font-semibold">PKR {{ Number(supply.total_amount || 0).toLocaleString() }}</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Purchase Order Details -->
                <Card>
                    <CardHeader>
                        <CardTitle>Purchase Order</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">PO Number</p>
                            <p class="font-semibold">{{ supply.purchase_order.po_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Institution</p>
                            <p>{{ supply.purchase_order.institution_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">PO Status</p>
                            <Badge>{{ supply.purchase_order.status }}</Badge>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Items Supplied -->
            <Card>
                <CardHeader>
                    <CardTitle>Items Supplied</CardTitle>
                    <CardDescription>
                        List of items included in this supply
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
                                <tr v-for="item in supply.items" :key="item.id" class="border-b">
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
            <Card>
                <CardHeader>
                    <CardTitle>Documents</CardTitle>
                    <CardDescription>
                        Generated and uploaded documents for this supply
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Generated Documents -->
                        <div class="space-y-2">
                            <h4 class="font-medium">Generated Documents</h4>
                            <div class="flex gap-2">
                                <a v-if="supply.dc_pdf" :href="`/storage/${supply.dc_pdf}`" target="_blank">
                                    <Button variant="outline" size="sm">
                                        <Download class="mr-2 h-4 w-4" />
                                        Download DC
                                    </Button>
                                </a>
                                <a v-if="supply.invoice_pdf" :href="`/storage/${supply.invoice_pdf}`" target="_blank">
                                    <Button variant="outline" size="sm">
                                        <Download class="mr-2 h-4 w-4" />
                                        Download Invoice
                                    </Button>
                                </a>
                            </div>
                        </div>

                        <!-- Stamped Documents -->
                        <div class="space-y-2">
                            <h4 class="font-medium">Stamped Documents</h4>
                            <div class="flex gap-2">
                                <a v-if="supply.dc_stamped" :href="`/storage/${supply.dc_stamped}`" target="_blank">
                                    <Button variant="outline" size="sm">
                                        <Download class="mr-2 h-4 w-4" />
                                        Stamped DC
                                    </Button>
                                </a>
                                <a v-if="supply.invoice_stamped" :href="`/storage/${supply.invoice_stamped}`" target="_blank">
                                    <Button variant="outline" size="sm">
                                        <Download class="mr-2 h-4 w-4" />
                                        Stamped Invoice
                                    </Button>
                                </a>
                                <Button v-if="!showUploadForm" variant="outline" size="sm" @click="showUploadForm = true">
                                    <Upload class="mr-2 h-4 w-4" />
                                    Upload Stamped
                                </Button>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Form -->
                    <div v-if="showUploadForm" class="border rounded-lg p-4 mt-4">
                        <h4 class="font-medium mb-4">Upload Stamped Documents</h4>
                        <form @submit.prevent="submitUpload" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label for="dc_stamped">Stamped DC</Label>
                                    <Input
                                        id="dc_stamped"
                                        type="file"
                                        accept=".pdf"
                                        @input="uploadForm.dc_stamped = $event.target?.files?.[0] || null"
                                        :class="{ 'border-red-500': uploadForm.errors.dc_stamped }"
                                    />
                                    <p class="text-sm text-muted-foreground">
                                        Upload the stamped DC document (PDF, max 5MB)
                                    </p>
                                    <p v-if="uploadForm.errors.dc_stamped" class="text-sm text-red-500">
                                        {{ uploadForm.errors.dc_stamped }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="invoice_stamped">Stamped Invoice</Label>
                                    <Input
                                        id="invoice_stamped"
                                        type="file"
                                        accept=".pdf"
                                        @input="uploadForm.invoice_stamped = $event.target?.files?.[0] || null"
                                        :class="{ 'border-red-500': uploadForm.errors.invoice_stamped }"
                                    />
                                    <p class="text-sm text-muted-foreground">
                                        Upload the stamped invoice document (PDF, max 5MB)
                                    </p>
                                    <p v-if="uploadForm.errors.invoice_stamped" class="text-sm text-red-500">
                                        {{ uploadForm.errors.invoice_stamped }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex justify-end gap-2">
                                <Button type="button" variant="outline" @click="showUploadForm = false">
                                    Cancel
                                </Button>
                                <Button type="submit" :disabled="uploadForm.processing">
                                    {{ uploadForm.processing ? 'Uploading...' : 'Upload Documents' }}
                                </Button>
                            </div>
                        </form>
                    </div>
                </CardContent>
            </Card>

            <!-- Payments -->
            <Card v-if="supply.payments.length > 0">
                <CardHeader>
                    <CardTitle>Payment History</CardTitle>
                    <CardDescription>
                        Payments submitted for this supply
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div v-for="payment in supply.payments" :key="payment.id" class="border rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold">Payment #{{ payment.id }}</p>
                                <p class="text-sm text-muted-foreground">
                                    Cheque: {{ payment.cheque_no }} • PKR {{ Number(payment.amount).toLocaleString() }}
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    Submitted: {{ new Date(payment.created_at).toLocaleDateString() }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <Badge :class="getPaymentStatusColor(payment.status)">
                                    {{ payment.status }}
                                </Badge>
                                <Link :href="route('payments.show', payment.id)">
                                    <Button variant="outline" size="sm">
                                        <Eye class="h-4 w-4" />
                                    </Button>
                                </Link>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Actions -->
            <div class="flex justify-end gap-4">
                <Link :href="route('supplies.index')">
                    <Button variant="outline">
                        Back to Supplies
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>