<template>
  <Head title="Thành viên"/>

  <AuthenticatedLayout>
    <template #header>
      Nhân viên Sales OneBss
    </template>

    <div class="p-4 bg-white rounded-lg shadow-xs">
      <div class="overflow-hidden mb-8 w-full rounded-lg border shadow-xs">
        <div class="overflow-x-auto w-full">
          <table class="w-full whitespace-no-wrap">
            <thead>
            <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase bg-gray-50 border-b">
              <th class="px-4 py-3">Tên NV</th>
              <th class="px-4 py-3">Tài khoản</th>
              <th class="px-4 py-3">Số KH quản lý</th>
              <th class="px-4 py-3">Thao tác</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y">
              <tr v-for="user in users.data" :key="user.id" class="text-gray-700">
                <td class="px-4 py-3 text-sm">
                  {{ user.name }}
                </td>
                <td class="px-4 py-3 text-sm">
                  {{ user.username }}
                </td>
                <td class="px-4 py-3 text-sm">{{ user.onebss_customers_count }}</td>
                <td class="px-4 py-3 text-sm">
                  <div class="inline-flex rounded-md shadow-sm" role="group">
                    <button @click="del(user.id)" type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
                      <svg class="h-3 w-3 text-red-500"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <line x1="4" y1="7" x2="20" y2="7" />  <line x1="10" y1="11" x2="10" y2="17" />  <line x1="14" y1="11" x2="14" y2="17" />  <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />  <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="!users.total">
                <td class="px-4 py-3 text-sm text-center" colspan="4">Không có dữ liệu</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase bg-gray-50 border-t sm:grid-cols-9">
          <pagination :links="users.links" />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
import Pagination from '@/Components/Admin/Pagination.vue';
import { Head, router } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';
import SecondaryButton from '@/Components/Admin/SecondaryButton.vue';
import 'element-plus/es/components/message/style/css';
import 'element-plus/es/components/message-box/style/css';
import { ElMessage, ElMessageBox } from 'element-plus';

const props = defineProps({
  users: Object,
});

const currentUser = ref(null);

const actions = reactive({
  isDeleteUser: false,
});

const onCloseForm = (prop) => {
  actions[prop] = false;
};

const del = (user) => {
  ElMessageBox.confirm(
    'Bạn có chắc muốn xóa khách hàng của nhân viên này chứ?',
    'Warning',
    {
      confirmButtonText: 'OK',
      cancelButtonText: 'Cancel',
      type: 'warning',
    }
  ).then(() => {
    axios.delete(route('admin.onebss.users.destroy', {user})).then(({data}) => {
      ElMessage({
        type: 'success',
        message: data.msg,
      });
      router.reload({only: ['users']});
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
</script>
