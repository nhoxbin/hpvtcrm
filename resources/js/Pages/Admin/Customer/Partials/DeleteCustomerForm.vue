<template>
    <div>
      <Modal :show="isDeleteUser" @close="closeModal">
        <div class="p-6">
          <h2 class="text-lg font-medium text-gray-900">
            Are you sure you want to delete this account?
          </h2>

          <p class="mt-1 text-sm text-gray-600">
            Once your account is deleted, all of its resources and data will be permanently deleted. Please
            enter your password to confirm you would like to permanently delete your account.
          </p>

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
        </div>
      </Modal>
    </div>
  </template>
    
<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Admin/Modal.vue';
import SecondaryButton from '@/Components/Admin/SecondaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  isDeleteUser: Boolean,
  user: Number,
});

const form = useForm({
  user: props.user,
});

const deleteUser = () => {
  form.delete(route('admin.users.destroy'), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    // onError: () => passwordInput.value.focus(),
    onFinish: () => form.reset(),
  });
};
const emit = defineEmits(['closeDeleteUserForm']);

const closeModal = () => {
  emit('closeDeleteUserForm', false);

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
  