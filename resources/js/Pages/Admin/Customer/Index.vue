<template>
  <Head title="Customers"/>

  <AuthenticatedLayout>
    <template #header>
      Customers
    </template>

    <div class="p-4 bg-white rounded-lg shadow-xs mb-2">
      <SecondaryButton @click="isUploadCustomer = true">Upload</SecondaryButton>
      <PrimaryButton @click="exportCustomer">Export</PrimaryButton>
      <DangerButton @click="isDeleteCustomer = true">Delete</DangerButton>
    </div>

    <div class="p-4 bg-white rounded-lg shadow-xs">
      <div class="overflow-hidden mb-8 w-full rounded-lg border shadow-xs">
        <div class="overflow-x-auto w-full">
          <table class="w-full whitespace-no-wrap">
            <thead>
              <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase bg-gray-50 border-b">
                <th class="px-4 py-3">Số điện thoại</th>
                <th class="px-4 py-3">Gói hiện tại</th>
                <th class="px-4 py-3">Ngày bắt đầu</th>
                <th class="px-4 py-3">Ngày kết thúc</th>
                <th class="px-4 py-3">Gói có sẵn</th>
                <th class="px-4 py-3">Người làm việc</th>
                <th class="px-4 py-3">Trạng thái</th>
                <th class="px-4 py-3">Sales Ghi chú</th>
                <th class="px-4 py-3">Admin Ghi chú</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y">
              <tr v-for="customer in customers.data" :key="customer.id" class="text-gray-700">
                <td class="px-4 py-3 text-sm">{{ customer.phone }}</td>
                <td class="px-4 py-3 text-sm">{{ customer.data }}</td>
                <td class="px-4 py-3 text-sm">{{ customer.registered_at }}</td>
                <td class="px-4 py-3 text-sm">{{ customer.expired_at }}</td>
                <td class="px-4 py-3 text-sm">{{ customer.available_data }}</td>
                <td class="px-4 py-3 text-sm">{{ customer.user?.name }}</td>
                <td class="px-4 py-3 text-sm">{{ customer.state }}</td>
                <td class="px-4 py-3 text-sm">{{ customer.sales_note }}</td>
                <td class="px-4 py-3 text-sm">{{ customer.admin_note }}</td>
                <!-- <td>
                  <div class="btn-group">
                    <button class="btn btn-info" @click="isEditUser = true">Sửa</button>
                    <button class="btn btn-danger" @click="deleteUser(customer.id)">Xóa</button>
                  </div>
                </td> -->
              </tr>
              <tr v-if="!customers.data.length">
                <td class="px-4 py-3 text-sm text-center" colspan="9">Không có dữ liệu</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div
          class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase bg-gray-50 border-t sm:grid-cols-9">
          <pagination :links="customers.links" />
        </div>
      </div>
    </div>

    <UploadCustomerForm :users="users" :is-upload-customer="isUploadCustomer" @closeUploadCustomerForm="onCloseUploadCustomerForm"></UploadCustomerForm>
    <DeleteCustomerForm :users="users" :is-upload-customer="isUploadCustomer" @closeDeleteCustomerForm="onCloseUploadCustomerForm"></DeleteCustomerForm>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
import Pagination from '@/Components/Admin/Pagination.vue';
import { ref } from 'vue';
import SecondaryButton from '@/Components/Admin/SecondaryButton.vue';
import UploadCustomerForm from './Partials/UploadCustomerForm.vue';
import 'element-plus/es/components/message/style/css'
import { ElMessage } from 'element-plus'
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/Admin/PrimaryButton.vue';

const props = defineProps({
  auth: Object,
  customers: Object,
  users: Array,
});

const isUploadCustomer = ref(false);
const isEditCustomer = ref(false);
const isDeleteCustomer = ref(false);
const customerId = ref(null);
const isCreateUser = ref(false);
const passwordInput = ref(null);

const confirmUserDeletion = () => {
  isEditingUser.value = true;

  // nextTick(() => passwordInput.value.focus());
};

const onCloseUploadCustomerForm = () => {
  isUploadCustomer.value = false;
};

const onCloseEditCustomerForm = () => {
  isEditCustomer.value = false;
};

const onCloseDeleteCustomerForm = () => {
  isDeleteCustomer.value = false;
};

const exportCustomer = () => {
  axios.post(route('admin.customers.export')).then(function({data}) {
    ElMessage({
      message: data.msg,
      type: 'success',
    });
  }).catch(function(err) {
    console.log(err);
  });
};
</script>
