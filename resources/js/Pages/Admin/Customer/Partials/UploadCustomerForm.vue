<template>
    <div>
      <Modal :show="isUploadCustomer" @close="closeModal">
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
        <input class="block w-full mb-5 text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" type="file" @change="crmFileChange" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />

        <div class="relative p-4 w-full max-w-md max-h-full">
          <!-- Modal content -->
          <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
              <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                Sửa
              </h3>
              <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" @click="closeModal">
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
                    type="text"
                    class="mt-1 block w-3/4"
                    placeholder="Name"
                  />
  
                  <InputError :message="form.errors.password" class="mt-2" />
                </div>
                <div>
                    <select class="form-control" v-model="user_id" multiple size="10">
                        <option value="all">Chia đều</option>
                        <option v-for="user in users" :key="user.id" :value="user.id">Chia đều</option>
                    </select>
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
  </template>
    
  <script setup>
  import InputError from '@/Components/Admin/InputError.vue';
  import InputLabel from '@/Components/Admin/InputLabel.vue';
  import Modal from '@/Components/Admin/Modal.vue';
  import TextInput from '@/Components/Admin/TextInput.vue';
  import { useForm } from '@inertiajs/vue3';
  import { ref } from 'vue';
  
  const props = defineProps({
    isUploadCustomer: Boolean
  });
  
  const passwordInput = ref(null);
  
  const form = useForm({
    'name': '',
    'username': '',
    'password': '',
    'password_confirmation': '',
  });
  
  /* const deleteUser = () => {
    form.delete(route('profile.destroy'), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
      // onError: () => passwordInput.value.focus(),
      onFinish: () => form.reset(),
    });
  }; */
  const emit = defineEmits(['closeUploadCustomerForm']);
  
  const closeModal = () => {
    emit('closeEditUserForm', false);
  
    form.reset();
  };
  
  function editUser(user) {
    this.modalLabel = 'Sửa';
    // $('#addUserForm').prepend('<input type="hidden" name="_method" value="patch" />');
    axios({
      url: route('admin.users.edit', user),
      method: 'get',
    }).then((resp) => {
      this.name = resp.name;
      this.username = resp.username;
      this.password = '';
      this.role = resp.role.id;
      // this.linkActionSaveOrUpdateUser = route('admin.users.update', id);
    });
  }
  </script>
  