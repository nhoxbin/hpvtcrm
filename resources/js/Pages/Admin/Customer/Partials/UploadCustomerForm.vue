<template>
  <div>
    <Modal :show="isUploadCustomer" @close="closeModal">
      <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
        <!-- Modal header -->
        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
          <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
            Tải lên tệp khách hàng
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
          <input class="block w-full mb-5 text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" type="file" @change="crmFileChange" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
          <form class="space-y-4" @submit.prevent="uploadFile">
            <div>
              <select class="form-control" v-model="selected_users" multiple size="10">
                <option value="all">Chia đều</option>
                <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
              </select>
            </div>
            <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Upload</button>
          </form>
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
import 'element-plus/es/components/message/style/css'
import { ElMessage } from 'element-plus'

const props = defineProps({
  users: Array,
  isUploadCustomer: Boolean
});

const selected_users = ref([]);
const crmFile = ref(null);

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
  emit('closeUploadCustomerForm', false);
};

const crmFileChange = (e) => {
  crmFile.value = e.target.files[0];
}

const uploadFile = () => {
  let formData = new FormData();
  formData.append('user_id', selected_users.value);
  formData.append('excel', crmFile.value);

  axios.post(route('admin.customers.store'), formData).then(function({data}) {
    ElMessage({
      message: data.msg,
      type: 'success',
    });
    closeModal();
  }).catch(function(resp) {
    alert(resp.responseText);
  });
}
</script>
  