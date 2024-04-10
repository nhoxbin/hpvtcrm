<template>
  <Head title="Khách hàng"/>

  <AuthenticatedLayout>
    <template #header>
      Admin Login DigiShop
    </template>

    <form @submit.prevent="form.patch(route('admin.digishop.update'))" class="mt-6 space-y-6">
        <div>
          <InputLabel for="username" value="Username" />

          <TextInput
            id="username"
            type="text"
            class="mt-1 block w-full"
            v-model="form.username"
            required
            autofocus
            autocomplete="username"
          />

          <InputError class="mt-2" :message="form.errors.username" />
        </div>

        <div>
          <InputLabel for="password" value="Password" />

          <TextInput
            id="password"
            type="password"
            class="mt-1 block w-full"
            v-model="form.password"
            required
            autocomplete="username"
          />

          <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <div class="flex items-center gap-4">
          <PrimaryButton :disabled="form.processing">Đăng nhập</PrimaryButton>

          <Transition
            enter-active-class="transition ease-in-out"
            enter-from-class="opacity-0"
            leave-active-class="transition ease-in-out"
            leave-to-class="opacity-0"
          >
            <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Thành công.</p>
          </Transition>
        </div>
    </form>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
// import Pagination from '@/Components/Admin/Pagination.vue';
// import { reactive, ref } from 'vue';
// import SecondaryButton from '@/Components/Admin/SecondaryButton.vue';
// import EditCustomerForm from './Partials/EditCustomerForm.vue';
// import UploadCustomerForm from './Partials/UploadCustomerForm.vue';
// import DeleteCustomerForm from './Partials/DeleteCustomerForm.vue';
import 'element-plus/es/components/message/style/css';
import 'element-plus/es/components/message-box/style/css';
// import { ElMessage, ElMessageBox } from 'element-plus';
// import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/Admin/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
// import { Head, router } from '@inertiajs/vue3';
// import { Link } from '@inertiajs/vue3';

import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
  auth: Object,
  customers: Object,
  users: Array,
});

const form = useForm({
  name: '',
  email: '',
});
</script>
