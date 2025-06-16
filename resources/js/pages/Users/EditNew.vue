<template>
  <AppLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Edit User
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <form @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-6">
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <CustomInput
                  id="name"
                  type="text"
                  class="mt-1 block w-full"
                  v-model="form.name"
                  autocomplete="name"
                />
                <span v-if="form.errors.name" class="text-red-500 text-sm mt-2">{{ form.errors.name }}</span>
              </div>

              <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <CustomInput
                  id="email"
                  type="email"
                  class="mt-1 block w-full"
                  v-model="form.email"
                  autocomplete="email"
                />
                <span v-if="form.errors.email" class="text-red-500 text-sm mt-2">{{ form.errors.email }}</span>
              </div>

              <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                <select
                  id="role"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  v-model="form.role"
                >
                  <option value="Admin">Admin</option>
                  <option value="User">User</option>
                </select>
                <span v-if="form.errors.role" class="text-red-500 text-sm mt-2">{{ form.errors.role }}</span>
              </div>

              <div class="flex items-center justify-end">
                <CustomButton
                  type="button"
                  class="mr-2 bg-gray-300 text-gray-700 hover:bg-gray-400"
                  @click="$inertia.visit(route('users.index'))"
                >
                  Cancel
                </CustomButton>
                <CustomButton
                  type="submit"
                  class="bg-blue-600 text-white hover:bg-blue-700"
                  :disabled="form.processing"
                >
                  Save
                </CustomButton>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button as CustomButton } from '@/components/ui/button';
import { Input as CustomInput } from '@/components/ui/input';
import { useForm } from '@inertiajs/inertia-vue3';

export default {
  components: {
    AppLayout,
    CustomButton,
    CustomInput,
  },

  props: {
    user: {
      type: Object,
      required: true,
    },
  },

  setup(props) {
    const form = useForm({
      name: props.user.name,
      email: props.user.email,
      role: props.user.role,
    });

    const submit = () => {
      form.put(route('users.update', props.user.id));
    };

    return { form, submit };
  },
};
</script>
