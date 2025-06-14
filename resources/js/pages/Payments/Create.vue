<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

interface Supply {
    id: number;
    supply_date: string;
    total_amount: number;
    purchase_order: {
        po_number: string;
        institution_name: string;
    };
    items: Array<{
        quantity: number;
        po_item: {
            name: string;
            price: number;
        };
    }>;
}

interface Props {
    supplies: Supply[];
    selectedSupply?: number;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Payments', href: '/payments' },
    { title: 'Create', href: '/payments/create' },
];

const formatAmount = (amount: number | null | undefined): number => {
    const numAmount = Number(amount);
    return isNaN(numAmount) ? 0 : parseFloat(numAmount.toFixed(2));
};

const selectedSupplyId = ref<number | null>(props.selectedSupply || null);
const selectedSupply = computed(() => {
    return props.supplies.find(supply => supply.id === selectedSupplyId.value) || null;
});

const form = useForm({
    supply_id: props.selectedSupply || null,
    cheque_no: '',
    amount: 0,
    cheque_image: null as File | null,
});

const selectSupply = (supplyId: number) => {
    selectedSupplyId.value = supplyId;
    const supply = selectedSupply.value;
    if (supply) {
        form.supply_id = supplyId;
        form.amount = formatAmount(supply.total_amount);
    }
};

const isAmountValid = computed(() => {
    if (!selectedSupply.value) return false;
    const total = formatAmount(selectedSupply.value.total_amount);
    const payment = formatAmount(form.amount);
    return payment > 0 && payment <= total;
});

const canSubmit = computed(() => {
    return (
        !form.processing &&
        selectedSupply.value !== null &&
        form.cheque_no.trim() !== '' &&
        isAmountValid.value
    );
});

const submit = () => {
    if (!canSubmit.value) {
        return;
    }

    form.post(route('payments.store'), {
        preserveScroll: true,
        onError: (errors) => {
            console.error('Payment submission failed:', errors);
        },
    });
};

onMounted(() => {
    if (props.selectedSupply && props.supplies.length > 0) {
        const supply = props.supplies.find(s => s.id === props.selectedSupply);
        if (supply) {
            selectSupply(props.selectedSupply);
        }
    }
});
</script>

<template>
    <Head title="Submit Payment" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Submit Payment</h1>
                <p class="text-muted-foreground">Submit payment details for a completed supply</p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Supply Selection -->
                <Card>
                    <CardHeader>
                        <CardTitle>Select Supply</CardTitle>
                        <CardDescription>Choose the supply for which you want to submit payment</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="supplies.length === 0" class="text-center py-8">
                            <p class="text-muted-foreground">No supplies available for payment.</p>
                            <p class="text-sm text-muted-foreground mt-2">
                                All supplies either have confirmed payments or no supplies exist.
                            </p>
                        </div>

                        <div v-else class="grid gap-4">
                            <div v-for="supply in supplies" :key="supply.id"
                                class="border rounded-lg p-4 cursor-pointer transition-colors hover:bg-muted/50"
                                :class="{ 'border-primary bg-primary/5': selectedSupplyId === supply.id }"
                                @click="selectSupply(supply.id)">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-semibold">Supply #{{ supply.id }}</h4>
                                        <p class="text-sm text-muted-foreground">
                                            {{ supply.purchase_order.po_number }} • {{ supply.purchase_order.institution_name }}
                                        </p>
                                        <p class="text-sm text-muted-foreground">
                                            Supply Date: {{ new Date(supply.supply_date).toLocaleDateString() }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold">PKR {{ formatAmount(supply.total_amount).toLocaleString() }}</p>
                                        <p class="text-sm text-muted-foreground">{{ supply.items.length }} items</p>
                                    </div>
                                </div>

                                <div v-if="selectedSupplyId === supply.id" class="mt-4 pt-4 border-t">
                                    <h5 class="font-medium mb-2">Items Supplied:</h5>
                                    <div class="space-y-1">
                                        <div v-for="item in supply.items" :key="item.po_item.name" class="flex justify-between text-sm">
                                            <span>{{ item.po_item.name }}</span>
                                            <span>
                                                {{ item.quantity }} × PKR {{ formatAmount(item.po_item.price).toLocaleString() }} = 
                                                PKR {{ formatAmount(item.quantity * item.po_item.price).toLocaleString() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Payment Form -->
                <template v-if="selectedSupply">
                    <Card>
                        <CardHeader>
                            <CardTitle>Payment Details</CardTitle>
                            <CardDescription>Enter payment details for Supply #{{ selectedSupply.id }}</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label for="cheque_no">Cheque Number</Label>
                                    <Input
                                        id="cheque_no"
                                        v-model="form.cheque_no"
                                        placeholder="Enter cheque number"
                                        :class="{ 'border-red-500': form.errors.cheque_no }"
                                        required
                                    />
                                    <p v-if="form.errors.cheque_no" class="text-sm text-red-500">
                                        {{ form.errors.cheque_no }}
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        Enter the cheque number exactly as it appears on the cheque
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="amount">Amount (PKR)</Label>
                                    <Input
                                        id="amount"
                                        type="number"
                                        step="0.01"
                                        v-model="form.amount"
                                        placeholder="0.00"
                                        :class="{ 'border-red-500': form.errors.amount }"
                                        required
                                        :min="0"
                                        :max="formatAmount(selectedSupply.total_amount)"
                                    />
                                    <p v-if="form.errors.amount" class="text-sm text-red-500">
                                        {{ form.errors.amount }}
                                    </p>
                                    <p v-if="!isAmountValid" class="text-sm text-red-500">
                                        Amount must be between 0 and PKR {{ formatAmount(selectedSupply.total_amount).toLocaleString() }}
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="cheque_image">Cheque Image</Label>
                                <Input
                                    id="cheque_image"
                                    type="file"
                                    accept="image/*"
                                    @input="form.cheque_image = $event.target?.files?.[0] || null"
                                    :class="{ 'border-red-500': form.errors.cheque_image }"
                                />
                                <p class="text-sm text-muted-foreground">
                                    Upload a clear, readable image of the cheque (optional)
                                </p>
                                <p v-if="form.errors.cheque_image" class="text-sm text-red-500">
                                    {{ form.errors.cheque_image }}
                                </p>
                            </div>

                            <div class="bg-muted/50 rounded-lg p-4 mt-4">
                                <h4 class="font-medium mb-2">Payment Summary</h4>
                                <div class="space-y-1 text-sm">
                                    <div class="flex justify-between">
                                        <span>Supply Amount:</span>
                                        <span>PKR {{ formatAmount(selectedSupply.total_amount).toLocaleString() }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Payment Amount:</span>
                                        <span>PKR {{ formatAmount(form.amount).toLocaleString() }}</span>
                                    </div>
                                    <div class="flex justify-between font-medium pt-2 border-t">
                                        <span>Status:</span>
                                        <span class="text-yellow-600">Pending Confirmation</span>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </template>

                <div class="flex justify-end gap-4">
                    <Button type="button" variant="outline" @click="$inertia.visit(route('payments.index'))">
                        Cancel
                    </Button>
                    <Button type="submit" :disabled="!canSubmit">
                        {{ form.processing ? 'Submitting...' : 'Submit Payment' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
