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
                  <button class="btn btn-info" @click="editUser(user.id)" data-toggle="modal" data-target="#modal-add-user">Sửa</button>
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
      <Modal :show="isEditingUser || isAddingUser" @close="closeModal">
        <!-- <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Are you sure you want to delete your account?
            </h2>
  
            <p class="mt-1 text-sm text-gray-600">
                Once your account is deleted, all of its resources and data will be permanently deleted. Please
                enter your password to confirm you would like to permanently delete your account.
            </p>
  
            <div class="mt-6">
                <InputLabel for="password" value="Password" class="sr-only" />
  
                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="Password"
                    @keyup.enter="deleteUser"
                />
  
                <InputError :message="form.errors.password" class="mt-2" />
            </div>
  
            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
  
                <DangerButton
                    class="ms-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="deleteUser"
                >
                    Delete Account
                </DangerButton>
            </div>
        </div> -->
        <div class="relative p-4 w-full max-w-md max-h-full">
          <!-- Modal content -->
          <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
              <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                {{ modalLabel }}
              </h3>
              <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="authentication-modal">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Close modal</span>
              </button>
            </div>
            <!-- Modal body -->
            <div class="p-4 md:p-5">
              <form class="space-y-4" action="#">
                  <div>
                    <InputLabel for="password" value="Password" class="sr-only" />
  
                    <TextInput
                      id="name"
                      v-model="form.name"
                      type="password"
                      class="mt-1 block w-3/4"
                      placeholder="Name"
                      @keyup.enter="addUser"
                    />
  
                    <InputError :message="form.errors.password" class="mt-2" />
                  </div>
                  <div>
                      <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your password</label>
                      <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                  </div>
                  <div class="flex justify-between">
                      <div class="flex items-start">
                          <div class="flex items-center h-5">
                              <input id="remember" type="checkbox" value="" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-600 dark:border-gray-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800" required>
                          </div>
                          <label for="remember" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Remember me</label>
                      </div>
                      <a href="#" class="text-sm text-blue-700 hover:underline dark:text-blue-500">Lost Password?</a>
                  </div>
                  <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Login to your account</button>
                  <div class="text-sm font-medium text-gray-500 dark:text-gray-300">
                      Not registered? <a href="#" class="text-blue-700 hover:underline dark:text-blue-500">Create account</a>
                  </div>
              </form>
            </div>
          </div>
        </div>
      </Modal>
    </div>
    </AuthenticatedLayout>
  </template>
  
  <script setup>
  import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
  import Pagination from '@/Components/Admin/Pagination.vue';
  import Modal from '@/Components/Admin/Modal.vue';
  import { Head } from '@inertiajs/vue3';
  
  const props = defineProps({
    auth: Object,
    users: Object
  });
  
  const isEditingUser = ref(false);
  const isAddingUser = ref(false);
  const passwordInput = ref(null);
  
  const form = useForm({
    'name': '',
    'username': '',
    'password': '',
  });
  
  const confirmUserDeletion = () => {
    isEditingUser.value = true;
  
    // nextTick(() => passwordInput.value.focus());
  };
  
  /* const deleteUser = () => {
    form.delete(route('profile.destroy'), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
      // onError: () => passwordInput.value.focus(),
      onFinish: () => form.reset(),
    });
  }; */
  
  const closeModal = () => {
    editUser.value = false;
  
    form.reset();
  };
  
  function addUser() {
    // $('#addUserForm').find('input[name="_method"]').remove();
    this.modalLabel = 'Thêm';
    this.name = '';
    this.username = '';
    this.password = '';
    this.role = 'Sales';
    this.linkActionSaveOrUpdateUser = route('admin.users.store');
  }
  </script>
  