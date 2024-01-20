<template>
  <div>
    <Modal :show="isCreateUser" @close="closeModal" max-width="xl">
      <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
        <!-- Modal header -->
        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
          <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
            Thêm tài khoản
          </h3>
          <button type="button" @click="closeModal" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
            <span class="sr-only">Close modal</span>
          </button>
        </div>
        <!-- Modal body -->
        <div class="p-4 md:p-5">
          <form class="max-w-sm mx-auto" action="#" @submit.prevent="submit">
            <div class="mb-5">
              <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tên</label>
              <input type="text" id="name" v-model="form.name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
              <InputError :message="form.errors.name" class="mt-2" />
            </div>
            <div class="mb-5">
              <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tên đăng nhập</label>
              <input type="text" id="username" v-model="form.username" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
              <InputError :message="form.errors.username" class="mt-2" />
            </div>
            <div class="mb-5">
              <label for="roles" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Quyền</label>
              <select id="roles" v-model="form.role" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="">Chọn quyền</option>
                <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
              </select>
              <InputError :message="form.errors.role" class="mt-2" />
            </div>
            <div class="mb-5">
              <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Mật khẩu</label>
              <input type="text" id="password" v-model="form.password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
              <InputError :message="form.errors.password" class="mt-2" />
            </div>
            <div class="mb-5">
              <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nhập lại mật khẩu</label>
              <input type="text" id="password_confirmation" v-model="form.password_confirmation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
              <InputError :message="form.errors.password_confirmation" class="mt-2" />
            </div>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Tạo tài khoản</button>
          </form>
        </div>
      </div>
    </Modal>
  </div>
</template>
  
<script setup>
import InputError from '@/Components/Admin/InputError.vue';
import Modal from '@/Components/Admin/Modal.vue';
import { router, useForm } from '@inertiajs/vue3';

const props = defineProps({
  roles: Object,
  isCreateUser: Boolean,
});

const form = useForm({
  name: '',
  role: '',
  username: '',
  password: '',
  password_confirmation: '',
});

const emit = defineEmits(['closeForm']);

const closeModal = () => {
  emit('closeForm', 'isCreateUser');

  form.reset();
};

const submit = () => {
  form.post(route('admin.users.store'), {
    onSuccess: () => closeModal(),
  });
};
</script>
