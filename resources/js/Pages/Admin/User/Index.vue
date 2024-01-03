<template>
  <Head title="Users"/>

  <AuthenticatedLayout>
    <template #header>
      Users
    </template>

    <div class="p-4 bg-white rounded-lg shadow-xs">
      <!-- <div class="inline-flex overflow-hidden mb-4 w-full bg-white rounded-lg shadow-md">
        <div class="flex justify-center items-center w-12 bg-blue-500">
          <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M20 3.33331C10.8 3.33331 3.33337 10.8 3.33337 20C3.33337 29.2 10.8 36.6666 20 36.6666C29.2 36.6666 36.6667 29.2 36.6667 20C36.6667 10.8 29.2 3.33331 20 3.33331ZM21.6667 28.3333H18.3334V25H21.6667V28.3333ZM21.6667 21.6666H18.3334V11.6666H21.6667V21.6666Z"></path>
          </svg>
        </div>

        <div class="px-4 py-2 -mx-3">
          <div class="mx-3">
            <span class="font-semibold text-blue-500">Info</span>
            <p class="text-sm text-gray-600">Sample table page</p>
          </div>
        </div>
      </div> -->

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
                <td><!-- {{ user.role.name + (auth_id == user.id ? '(Tôi)' : null) }} --></td>
                <td>{{ user.customers_count }}</td>
                <td>{{ user.created_by_user?.name }}</td>
                <td>
                  <div class="btn-group">
                    <button class="btn btn-info" @click="isEditUser = true">Sửa</button>
                    <button class="btn btn-danger" @click="deleteUser(user.id)">Xóa</button>
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

      <EditUserForm :is-edit-user="isEditUser" @close-edit-user-form="onCloseEditUserForm"></EditUserForm>
      <DeleteUserForm :is-delete-user="isDeleteUser" :user="deleteUserId" @close-delete-user-form="onCloseDeleteUserForm"></DeleteUserForm>
      <!-- <CreateUserForm :is-create-user="isCreateUser"></CreateUserForm> -->
      
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
import Pagination from '@/Components/Admin/Pagination.vue';
import EditUserForm from './Partials/EditUserForm.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
// import CreateUserForm from './Partials/CreateUserForm.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import SecondaryButton from '@/Components/Admin/SecondaryButton.vue';

const props = defineProps({
  auth: Object,
  users: Object
});

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

const deleteUser = (user_id) => {
  isDeleteUser.value = true;
  deleteUserId.value = user_id;
  /* form.delete(route('profile.destroy'), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    // onError: () => passwordInput.value.focus(),
    onFinish: () => form.reset(),
  }); */
};

/* const editingUser = (isEdit) => {
  isEditingUser.value = isEdit;
  isAddingUser.value = !isEdit;
}; */

const onCloseEditUserForm = () => {
  isEditUser.value = false;
};

const onCloseDeleteUserForm = () => {
  isDeleteUser.value = false;
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
      alert('Xóa thành công');
      location.reload();
    }).catch(function(err) {
      console.log(err.responseText);
    });
  }
}
</script>
