<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Edit Purchase Order
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <form @submit.prevent="submit">            <!-- Purchase Order Form Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="po_number" class="block text-sm font-medium text-gray-700">PO Number *</label>
                <input
                  id="po_number"
                  v-model="form.po_number"
                  type="text"
                  required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                />
                <p v-if="form.errors.po_number" class="mt-1 text-sm text-red-600">
                  {{ form.errors.po_number }}
                </p>
              </div>

              <div>
                <label for="po_date" class="block text-sm font-medium text-gray-700">PO Date *</label>
                <input
                  id="po_date"
                  v-model="form.po_date"
                  type="date"
                  required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                />
                <p v-if="form.errors.po_date" class="mt-1 text-sm text-red-600">
                  {{ form.errors.po_date }}
                </p>
              </div>

              <div>
                <label for="institution_name" class="block text-sm font-medium text-gray-700">Institution Name</label>
                <input
                  id="institution_name"
                  v-model="form.institution_name"
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                />
              </div>

              <div>
                <label for="institution_email" class="block text-sm font-medium text-gray-700">Institution Email</label>
                <input
                  id="institution_email"
                  v-model="form.institution_email"
                  type="email"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                />
              </div>

              <div>
                <label for="institution_phone" class="block text-sm font-medium text-gray-700">Institution Phone</label>
                <input
                  id="institution_phone"
                  v-model="form.institution_phone"
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                />
              </div>

              <div>
                <label for="institution_address" class="block text-sm font-medium text-gray-700">Institution Address</label>
                <textarea
                  id="institution_address"
                  v-model="form.institution_address"
                  rows="3"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                ></textarea>
              </div>

              <div>
                <label for="po_image" class="block text-sm font-medium text-gray-700">PO Image</label>
                <input
                  id="po_image"
                  type="file"
                  @input="form.po_image = ($event.target as HTMLInputElement).files?.[0] ?? null"
                  accept="image/*"
                  class="mt-1 block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold
                    file:bg-blue-50 file:text-blue-700
                    hover:file:bg-blue-100"
                />
                <p v-if="form.errors.po_image" class="mt-1 text-sm text-red-600">
                  {{ form.errors.po_image }}
                </p>
              </div>
            </div>

            <!-- Items Section -->
            <div class="mt-8">
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Items</h3>
                <button
                  type="button"
                  @click="addItem"
                  class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                >
                  Add Item
                </button>
              </div>

              <div v-for="(item, index) in form.items" :key="index" class="bg-gray-50 p-4 rounded-lg mb-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Item Name</label>
                    <input
                      v-model="item.name"
                      type="text"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                    />
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Price</label>
                    <input
                      v-model.number="item.price"
                      type="number"
                      step="0.01"
                      @input="updateItemTotal(item)"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700">Quantity</label>
                    <input
                      v-model.number="item.quantity"
                      type="number"
                      min="1"
                      @input="updateItemTotal(item)"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700">Batch No.</label>
                    <input
                      v-model="item.batch_no"
                      type="text"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700">Manufacturing Date</label>
                    <input
                      v-model="item.mfg_date"
                      type="date"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700">Expiry Date</label>
                    <input
                      v-model="item.exp_date"
                      type="date"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700">Total Amount</label>
                    <input
                      :value="item.total_amount"
                      type="number"
                      step="0.01"
                      readonly
                      class="mt-1 block w-full rounded-md bg-gray-100 border-gray-300 shadow-sm"
                    />
                  </div>

                  <div class="flex items-end">
                    <button
                      type="button"
                      @click="removeItem(index)"
                      class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700"
                    >
                      Remove
                    </button>
                  </div>
                </div>
              </div>              <div v-if="form.items.length > 0" class="mt-4 text-right">
                <p class="text-lg font-semibold">
                  Total Amount: {{ Number(totalAmount).toFixed(2) }}
                </p>
              </div>
            </div>

            <div class="flex items-center justify-end mt-6">
              <button
                type="button"
                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-300 focus:outline-none focus:border-gray-300 focus:ring focus:ring-gray-200 disabled:opacity-25 transition mr-4"
                @click="cancel"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition"
              >
                Update
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

export default {
  components: {
    AppLayout,
  },

  props: {
    purchaseOrder: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    const form = useForm({
      po_number: props.purchaseOrder.po_number,
      po_date: props.purchaseOrder.po_date,
      institution_name: props.purchaseOrder.institution_name,
      institution_email: props.purchaseOrder.institution_email,
      institution_phone: props.purchaseOrder.institution_phone,
      institution_address: props.purchaseOrder.institution_address,
      items: props.purchaseOrder.items ? props.purchaseOrder.items.map((item: { id: any; name: any; price: number; quantity: number; batch_no: any; mfg_date: any; exp_date: any; }) => ({
        id: item.id,
        name: item.name,
        price: item.price,
        quantity: item.quantity,
        batch_no: item.batch_no,
        mfg_date: item.mfg_date,
        exp_date: item.exp_date,
        total_amount: item.price * item.quantity
      })) : [],
      po_image: null as File | null
    });

    const submit = () => {
      // Ensure all items have their total_amount calculated before submitting
      form.items.forEach((item: { price: number; quantity: number; total_amount: number }) => {
        item.total_amount = item.price * item.quantity;
      });
      form.put(route('purchase-orders.update', props.purchaseOrder.id));
    };

    const cancel = () => {
      window.history.back();
    };

    const addItem = () => {
      form.items.push({
        name: '',
        price: 0,
        quantity: 1,
        batch_no: '',
        mfg_date: null,
        exp_date: null,
        total_amount: 0
      });
    };

    const removeItem = (index: number) => {
      form.items.splice(index, 1);
    };

    interface PurchaseOrderItem {
      id?: number;
      name: string;
      price: number;
      quantity: number;
      batch_no: string;
      mfg_date: string | null;
      exp_date: string | null;
      total_amount: number;
    }

    const updateItemTotal = (item: PurchaseOrderItem) => {
      item.total_amount = item.price * item.quantity;
    };    const totalAmount = computed(() => {
      const total = form.items.reduce((sum: number, item: { total_amount: number }) => {
        const itemTotal = parseFloat(item.total_amount as any) || 0;
        return sum + itemTotal;
      }, 0);
      return Number.isFinite(total) ? total : 0;
    });

    return { form, submit, cancel, addItem, removeItem, updateItemTotal, totalAmount };
  },
};
</script>
