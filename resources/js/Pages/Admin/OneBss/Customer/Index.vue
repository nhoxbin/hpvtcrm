<template>
  <Head title="Khách hàng" />

  <AuthenticatedLayout>
    <template #header> Khách hàng </template>

    <div class="p-4 bg-white rounded-lg shadow-xs min-w-0">
      <div class="flex items-center justify-between mb-4">
        <div class="flex space-x-2">
          <SecondaryButton @click="actions.isUploadCustomer = true"
            >Upload</SecondaryButton
          >
          <PrimaryButton @click="actions.isDistributeCustomer = true"
            >Phân phối</PrimaryButton
          >
          <PrimaryButton
            @click="exportExcel(route('admin.onebss.customers.export'))"
            >Export</PrimaryButton
          >
        </div>
        <DangerButton @click="actions.isDeleteCustomer = true"
          >Delete</DangerButton
        >
      </div>
      <div class="mb-4 w-full">
        <label for="auth_status">{{ auth_status }}</label>
      </div>
      <div class="mb-4 w-full">
        <label for="process"
          >Đã chạy:
          {{
            process_customers["processing"] + "/" + process_customers["total"]
          }}</label
        >
      </div>
      <!-- <div class="relative mb-4 flex flex-wrap items-stretch"> -->
      <form @submit.prevent="onSearching()">
        <div class="grid gap-6 mb-6 md:grid-cols-2 lg:grid-cols-3">
          <div>
            <label
              for="tra_sau"
              class="block mb-2 text-sm font-medium text-gray-900"
              >Loại thuê bao</label
            >
            <div class="flex items-center space-x-4 mt-2">
              <div class="flex items-center">
                <input
                  id="tra_truoc"
                  type="checkbox"
                  value="0"
                  v-model="formSearch.tra_sau"
                  class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                />
                <label
                  for="tra_truoc"
                  class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300"
                  >Trả trước</label
                >
              </div>
              <div class="flex items-center">
                <input
                  id="tra_sau"
                  type="checkbox"
                  value="1"
                  v-model="formSearch.tra_sau"
                  class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                />
                <label
                  for="tra_sau"
                  class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300"
                  >Trả sau</label
                >
              </div>
            </div>
          </div>
          <div>
            <label
              for="phone"
              class="block mb-2 text-sm font-medium text-gray-900"
              >Số điện thoại</label
            >
            <input
              type="text"
              id="phone"
              placeholder="VD: 849xxxxxxxx"
              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
              v-model="formSearch.phone"
            />
          </div>
          <div>
            <label
              for="expires_in"
              class="block mb-2 text-sm font-medium text-gray-900"
              >Trước ngày hết hạn</label
            >
            <input
              type="text"
              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
              placeholder="Trước ngày hết hạn"
              aria-label="Trước ngày hết hạn"
              v-model="formSearch.expires_in"
            />
          </div>

          <div>
            <label
              for="worked_user"
              class="block mb-2 text-sm font-medium text-gray-900"
              >Người làm việc</label
            >
            <select
              id="worked_user"
              v-model="formSearch.worked_user"
              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
            >
              <option value="0">Chọn thành viên</option>
              <option v-for="user in users" :key="user.id" :value="user.id">
                {{ user.name }}
              </option>
            </select>
          </div>
          <div>
            <label
              for="checked_by_user"
              class="block mb-2 text-sm font-medium text-gray-900"
              >Người check</label
            >
            <select
              id="checked_by_user"
              v-model="formSearch.checked_by_user"
              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
            >
              <option value="0">Chọn thành viên</option>
              <option v-for="user in users" :key="user.id" :value="user.id">
                {{ user.name }}
              </option>
            </select>
          </div>
          <div>
            <label
              for="sales_state"
              class="block mb-2 text-sm font-medium text-gray-900"
              >Trạng thái</label
            >
            <select
              id="sales_state"
              v-model="formSearch.sales_state"
              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
            >
              <option value="0">Chọn thành viên</option>
              <option
                v-for="(state, key) in sales_states"
                :key="key"
                :value="key"
              >
                {{ state }}
              </option>
            </select>
          </div>
        </div>
        <div class="mb-6">
          <label
            for="email"
            class="block mb-2 text-sm font-medium text-gray-900"
            >Gói</label
          >
          <vue3-tags-input
            :tags="formSearch.goi_data"
            @on-tags-changed="(newTags) => (formSearch.goi_data = newTags)"
          />
        </div>
        <div class="mb-6">
          <label
            for="email"
            class="block mb-2 text-sm font-medium text-gray-900"
            >Gói IR</label
          >
          <vue3-tags-input
            :tags="formSearch.goi_ir"
            @on-tags-changed="(newTags) => (formSearch.goi_ir = newTags)"
          />
        </div>
        <button
          type="submit"
          class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
        >
          Tìm kiếm
        </button>
      </form>
      <!-- </div> -->
      <div class="mb-8 w-full rounded-lg border shadow-xs">
        <div class="overflow-x-auto">
          <table class="w-full whitespace-nowrap">
            <thead>
              <tr
                class="text-xs font-semibold text-left text-gray-500 uppercase bg-gray-50 border-b"
              >
                <th class="px-4 py-3">Số điện thoại</th>
                <th class="px-4 py-3">Tài khoản chính</th>
                <th class="px-4 py-3">Loại thuê bao</th>
                <th class="px-4 py-3">Gói cước TS</th>
                <th class="px-4 py-3">Gói cước</th>
                <th class="px-4 py-3">Gói data</th>
                <th class="px-4 py-3">Gói IR</th>
                <th class="px-4 py-3">Gói</th>
                <th class="px-4 py-3">Ngày hết hạn</th>
                <th class="px-4 py-3">TÍch hợp</th>
                <th class="px-4 py-3">Người làm việc</th>
                <th class="px-4 py-3">Người check</th>
                <th class="px-4 py-3">Trạng thái</th>
                <th class="px-4 py-3">Sales Ghi chú</th>
                <th class="px-4 py-3">Admin Ghi chú</th>
                <th class="px-4 py-3">Thao tác</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y">
              <tr
                v-for="customer in customers.data"
                :key="customer.id"
                class="text-gray-700"
              >
                <td class="px-4 py-3 text-sm">{{ customer.phone }}</td>
                <td class="px-4 py-3 text-sm">
                  {{ vnd_format(customer.core_balance) }}
                </td>
                <td class="px-4 py-3 text-sm">
                  {{ get_trasau(customer.tra_sau) }}
                </td>
                <td class="px-4 py-3 text-sm">
                  {{ get_goi_data_to_string(customer.goi_cuoc_ts) }}
                </td>
                <td class="px-4 py-3 text-sm">
                  {{ get_goi_data_to_string(customer.goi_cuoc) }}
                </td>
                <td class="px-4 py-3 text-sm">
                  {{ get_goi_data_to_string(customer.goi_data) }}
                </td>
                <td class="px-4 py-3 text-sm">
                  {{ get_goi_data_to_string(customer.goi_ir) }}
                </td>
                <td class="px-4 py-3 text-sm">{{ customer.goi }}</td>
                <td class="px-4 py-3 text-sm">{{ customer.expired_at }}</td>
                <td class="px-4 py-3 text-sm">
                  {{ customer.integration }}
                </td>
                <td class="px-4 py-3 text-sm">{{ customer.user?.name }}</td>
                <td class="px-4 py-3 text-sm">
                  {{ customer.checked_by?.name }}
                </td>
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
                    <button
                      @click="edit(customer)"
                      type="button"
                      class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white"
                    >
                      <svg
                        class="h-3 w-3 text-red-500"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        fill="none"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      >
                        <path stroke="none" d="M0 0h24v24H0z" />
                        <path
                          d="M9 7 h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3"
                        />
                        <path
                          d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3"
                        />
                        <line x1="16" y1="5" x2="19" y2="8" />
                      </svg>
                    </button>
                    <button
                      @click="del(customer.id)"
                      type="button"
                      class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white"
                    >
                      <svg
                        class="h-3 w-3 text-red-500"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        fill="none"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      >
                        <path stroke="none" d="M0 0h24v24H0z" />
                        <line x1="4" y1="7" x2="20" y2="7" />
                        <line x1="10" y1="11" x2="10" y2="17" />
                        <line x1="14" y1="11" x2="14" y2="17" />
                        <path
                          d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"
                        />
                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="!customers.data.length">
                <td class="px-4 py-3 text-sm text-center" colspan="14">
                  Không có dữ liệu
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div
          class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase bg-gray-50 border-t sm:grid-cols-9"
        >
          <pagination :links="customers.links" />
        </div>
      </div>
    </div>

    <EditCustomerForm
      v-if="actions.isEditCustomer"
      :isEditCustomer="actions.isEditCustomer"
      :customer="currentCustomer"
      @closeForm="onCloseForm"
    />
    <UploadCustomerForm
      :isUploadCustomer="actions.isUploadCustomer"
      @closeForm="onCloseForm"
    />
    <DeleteCustomerForm
      :isDeleteCustomer="actions.isDeleteCustomer"
      :formSearch="formSearch"
      @closeForm="onCloseForm"
    />
    <DistributeCustomerForm
      :users="users"
      :isDistributeCustomer="actions.isDistributeCustomer"
      :formSearch="formSearch"
      @closeForm="onCloseForm"
    />
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/Admin/AuthenticatedLayout.vue";
import Pagination from "@/Components/Admin/Pagination.vue";
import { reactive, ref } from "vue";
import SecondaryButton from "@/Components/Admin/SecondaryButton.vue";
import EditCustomerForm from "./Partials/EditCustomerForm.vue";
import UploadCustomerForm from "./Partials/UploadCustomerForm.vue";
import DeleteCustomerForm from "./Partials/DeleteCustomerForm.vue";
import "element-plus/es/components/message/style/css";
import "element-plus/es/components/message-box/style/css";
import { ElMessage, ElMessageBox } from "element-plus";
import DangerButton from "@/Components/DangerButton.vue";
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import DistributeCustomerForm from "./Partials/DistributeCustomerForm.vue";
import PrimaryButton from "@/Components/Admin/PrimaryButton.vue";
import _, { debounce } from "lodash";
import Vue3TagsInput from "vue3-tags-input";
import { defineComponent } from "vue";

defineComponent({
  components: {
    Vue3TagsInput,
  },
});

const props = defineProps({
  auth: Object,
  customers: Object,
  users: Array,
  msg: String,
  error: String,
  process_customers: Object,
  auth_status: String,
  sales_states: {
    type: Object,
    required: true,
  },
});

if (props.msg) {
  ElMessage({
    type: "info",
    message: props.msg,
  });
}
if (props.error) {
  ElMessage({
    type: "info",
    message: props.error,
  });
}
const page = usePage();

const tra_sau = ref(page.props.query.tra_sau || []);
const formSearch = useForm({
  command: "", // delete
  user_id: ["all"], // distribute
  tra_sau: tra_sau,
  worked_user: page.props.query.worked_user || 0,
  checked_by_user: page.props.query.checked_by_user || 0,
  sales_state: page.props.query.sales_state || 0,
  phone: page.props.query.phone || "",
  goi_data: page.props.query.goi_data || [],
  goi_ir: page.props.query.goi_ir || [],
  expires_in: page.props.query.expires_in || "",
});

const currentCustomer = ref(null);

const actions = reactive({
  isUploadCustomer: false,
  isEditCustomer: false,
  isDeleteCustomer: false,
  isDistributeCustomer: false,
});

const vnd_format = (num) => {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  }).format(num);
};
/* const get_goidata = (goi_data) => {
  return _.join(_.map(goi_data, function(data) {
    return 'Tên gói: ' + data['PACKAGE_NAME'] + ', dịch vụ: ' + data['SERVICES'];
  }), "\n");
}; */
const get_trasau = (tra_sau) => {
  let label = null;
  if (tra_sau == 0) label = "Trả trước";
  else if (tra_sau == 1) label = "Trả sau";
  return label;
};
/* const get_expires_date = (goi_data) => {
  return _.join(_.map(goi_data, 'TIME_END'), "\n");
}; */

const get_goi_data_to_string = (goi_data) => {
  return _.join(
    _.map(goi_data, function (data) {
      return (
        "Tên gói: " +
        data["PACKAGE_NAME"] +
        ", dịch vụ: " +
        data["SERVICES"] +
        ", Ngày hết hạn: " +
        data["TIME_END"]
      );
    }),
    "\n"
  );
};

const exportExcel = (url) => {
  document.location = url;
};

const onCloseForm = (prop) => {
  actions[prop] = false;
};

const edit = (customer) => {
  currentCustomer.value = customer;
  actions.isEditCustomer = true;
};

const onSearching = debounce(() => {
  formSearch.get(route("admin.onebss.customers.index"), {
    preserveScroll: true,
    onSuccess: () => {},
  });
}, 500);

const del = (customer) => {
  ElMessageBox.confirm("Bạn có chắc muốn xóa khách hàng này chứ?", "Warning", {
    confirmButtonText: "OK",
    cancelButtonText: "Cancel",
    type: "warning",
  })
    .then(() => {
      axios
        .delete(route("admin.onebss.customers.destroy", customer))
        .then(({ data }) => {
          ElMessage({
            type: "success",
            message: "Xóa thành công",
          });
          router.reload({ only: ["customers"] });
        })
        .catch(function (err) {
          // console.log(err.responseText);
        });
    })
    .catch(() => {
      /* ElMessage({
      type: 'info',
      message: 'Delete cancelled',
    }) */
    });
};
</script>
