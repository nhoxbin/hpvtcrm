<template>
  <Head title="Users"/>

  <AuthenticatedLayout>
    <template #header>
      Thành viên
    </template>

    <div class="p-4 bg-white rounded-lg shadow-xs">
      <div class="mb-4 w-full">
        <!-- <div class="flex justify-center items-center w-12 bg-blue-500">
          <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M20 3.33331C10.8 3.33331 3.33337 10.8 3.33337 20C3.33337 29.2 10.8 36.6666 20 36.6666C29.2 36.6666 36.6667 29.2 36.6667 20C36.6667 10.8 29.2 3.33331 20 3.33331ZM21.6667 28.3333H18.3334V25H21.6667V28.3333ZM21.6667 21.6666H18.3334V11.6666H21.6667V21.6666Z"></path>
          </svg>
        </div> -->

        <SecondaryButton @click="isCreateUser = true">Thêm thành viên</SecondaryButton>
      </div>

      <div class="overflow-hidden mb-8 w-full rounded-lg border shadow-xs">
        <div class="overflow-x-auto w-full">
          <table class="w-full whitespace-no-wrap">
            <thead>
            <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase bg-gray-50 border-b">
              <th class="px-4 py-3">Tên NV</th>
              <th class="px-4 py-3">Tài khoản</th>
              <th class="px-4 py-3">Chức vụ</th>
              <th class="px-4 py-3">Số KH quản lý</th>
              <th class="px-4 py-3">Người quản lý</th>
              <th class="px-4 py-3">Hành động</th>
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
                <td>{{ user.roles.map(x => x['name']) + ($page.props.auth.user.id == user.id ? '(Tôi)' : '') }}</td>
                <td class="px-4 py-3 text-sm">{{ user.customers_count }}</td>
                <td class="px-4 py-3 text-sm">{{ user.created_by_user?.name }}</td>
                <td class="px-4 py-3 text-sm">
                  <div class="inline-flex rounded-md shadow-sm" role="group">
                    <button @click="editUser(user)" type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
                      <svg class="h-3 w-3 text-red-500"  viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <path d="M9 7 h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" />  <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3" />  <line x1="16" y1="5" x2="19" y2="8" /></svg>
                    </button>
                    <button @click="deleteUser(user.id)" type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
                      <svg class="h-3 w-3 text-red-500"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <line x1="4" y1="7" x2="20" y2="7" />  <line x1="10" y1="11" x2="10" y2="17" />  <line x1="14" y1="11" x2="14" y2="17" />  <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />  <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div
            class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase bg-gray-50 border-t sm:grid-cols-9">
          <pagination :links="users.links" />
        </div>
      </div>

      <CreateUserForm :is-create-user="isCreateUser" :roles="roles" @closeCreateUserForm="onCloseCreateUserForm(state)"></CreateUserForm>
      <EditUserForm :is-edit-user="isEditUser" :roles="roles" :user="currentUser" @closeCreateUserForm="onCloseEditUserForm(state)"></EditUserForm>
      <DeleteUserForm :is-delete-user="isDeleteUser" :user="deleteUserId" @closeDeleteUserForm="onCloseDeleteUserForm(state)"></DeleteUserForm>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
import Pagination from '@/Components/Admin/Pagination.vue';
import CreateUserForm from './Partials/CreateUserForm.vue';
import EditUserForm from './Partials/EditUserForm.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import SecondaryButton from '@/Components/Admin/SecondaryButton.vue';
import 'element-plus/es/components/message/style/css';
import { ElMessage } from 'element-plus';

const props = defineProps({
  sessionMsg: String,
  roles: Object,
  users: Object,
});

const currentUser = ref(null);
const isEditUser = ref(false);
const isDeleteUser = ref(false);
const deleteUserId = ref(null);
const isCreateUser = ref(false);
const passwordInput = ref(null);

/* const form = useForm({
  'name': '',
  'username': '',
  'password': '',
}); */

const confirmUserDeletion = () => {
  isEditingUser.value = true;

  // nextTick(() => passwordInput.value.focus());
};

if (props.sessionMsg) {
  ElMessage({
    message: props.sessionMsg,
    type: 'success',
  });
}

const deleteUser = (user) => {
  ElMessageBox.confirm(
    'Bạn có chắc muốn xóa data của nhân viên này chứ?',
    'Warning',
    {
      confirmButtonText: 'OK',
      cancelButtonText: 'Cancel',
      type: 'warning',
    }
  ).then(() => {
    axios.delete(route('admin.users.destroy', user)).then((resp) => {
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

/* const editingUser = (isEdit) => {
  isEditingUser.value = isEdit;
  isAddingUser.value = !isEdit;
}; */

const onCloseCreateUserForm = (state) => {
  isCreateUser.value = state;
};

const onCloseEditUserForm = (state) => {
  isEditUser.value = state;
};

const onCloseDeleteUserForm = (state) => {
  isDeleteUser.value = state;
};

const editUser = (user) => {
  currentUser.value = user;
  isEditUser.value = true;
};

function deleteData(id) {
  if (confirm('Bạn có chắc muốn xóa data của nhân viên này chứ?')) {
    axios({
      url: route('admin.customers.destroy'),
      method: 'post',
      data: {
        'command': ['user'],
        'user_id': id
      }
    }).then((resp) => {
      ElMessage({
        message: 'Xóa thành công',
        type: 'success',
      });
      location.reload();
    }).catch(function(err) {
      console.log(err.responseText);
    });
  }
}
</script>
