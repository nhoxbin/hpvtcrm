<template>
  <Head title="Khách hàng"/>

  <AuthenticatedLayout>
    <template #header>
      Khách hàng
    </template>

    <div class="p-4 bg-white rounded-lg shadow-xs">
      <div class="mb-4 w-full">
        <SecondaryButton @click="actions.isUploadCustomer = true">Upload</SecondaryButton>
        <PrimaryButton @click="exportCustomer">Export</PrimaryButton>
        <DangerButton @click="actions.isDeleteCustomer = true">Delete</DangerButton>
      </div>

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
                <th class="px-4 py-3">Thao tác</th>
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
                <td class="px-4 py-3 text-sm">
                  <div class="inline-flex rounded-md shadow-sm" role="group">
                    <button @click="edit(customer)" type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
                      <svg class="h-3 w-3 text-red-500"  viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <path d="M9 7 h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" />  <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3" />  <line x1="16" y1="5" x2="19" y2="8" /></svg>
                    </button>
                    <button @click="del(customer.id)" type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
                      <svg class="h-3 w-3 text-red-500"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <line x1="4" y1="7" x2="20" y2="7" />  <line x1="10" y1="11" x2="10" y2="17" />  <line x1="14" y1="11" x2="14" y2="17" />  <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />  <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                    </button>
                  </div>
                </td>
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

    <EditCustomerForm v-if="actions.isEditCustomer" :isEditCustomer="actions.isEditCustomer" :customer="currentCustomer" @closeForm="onCloseForm"></EditCustomerForm>
    <UploadCustomerForm :users="users" :isUploadCustomer="actions.isUploadCustomer" @closeForm="onCloseForm"></UploadCustomerForm>
    <DeleteCustomerForm :isDeleteCustomer="actions.isDeleteCustomer" @closeForm="onCloseForm"></DeleteCustomerForm>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
import Pagination from '@/Components/Admin/Pagination.vue';
import { reactive, ref } from 'vue';
import SecondaryButton from '@/Components/Admin/SecondaryButton.vue';
import EditCustomerForm from './Partials/EditCustomerForm.vue';
import UploadCustomerForm from './Partials/UploadCustomerForm.vue';
import DeleteCustomerForm from './Partials/DeleteCustomerForm.vue';
import 'element-plus/es/components/message/style/css';
import 'element-plus/es/components/message-box/style/css';
import { ElMessage, ElMessageBox } from 'element-plus';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/Admin/PrimaryButton.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
  auth: Object,
  customers: Object,
  users: Array,
});

const currentCustomer = ref(null);

const actions = reactive({
  isUploadCustomer: false,
  isEditCustomer: false,
  isDeleteCustomer: false,
});

const onCloseForm = (prop) => {
  actions[prop] = false;
};

const edit = (customer) => {
  currentCustomer.value = customer;
  actions.isEditCustomer = true;
};

const del = (customer) => {
  ElMessageBox.confirm(
    'Bạn có chắc muốn xóa khách hàng này chứ?',
    'Warning',
    {
      confirmButtonText: 'OK',
      cancelButtonText: 'Cancel',
      type: 'warning',
    }
  ).then(() => {
    axios.delete(route('admin.customers.destroy', {})).then(({data}) => {
      ElMessage({
        type: 'success',
        message: 'Xóa thành công',
      })
    }).catch(function(err) {
      console.log(err.responseText);
    });
  }).catch(() => {
    /* ElMessage({
      type: 'info',
      message: 'Delete cancelled',
    }) */
  })
}

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
