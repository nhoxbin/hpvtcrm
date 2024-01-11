import{r as u,T as _,o as y,c as w,a as o,w as a,d as s,n as h,b as c,u as t,h as x,i as g}from"./app-CTb-M7it.js";import{D as m}from"./DangerButton-MaNJq58t.js";import{_ as v,a as k,b as D}from"./TextInput-zuZ3BGlp.js";import{_ as b}from"./Modal-HvQIsPGa.js";import{_ as C}from"./SecondaryButton-w7D2jSMh.js";import"./_plugin-vue_export-helper-x3n3nnut.js";const V={class:"space-y-6"},B=s("header",null,[s("h2",{class:"text-lg font-medium text-gray-900"},"Delete Account"),s("p",{class:"mt-1 text-sm text-gray-600"}," Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain. ")],-1),U={class:"p-6"},$=s("h2",{class:"text-lg font-medium text-gray-900"}," Are you sure you want to delete your account? ",-1),A=s("p",{class:"mt-1 text-sm text-gray-600"}," Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account. ",-1),N={class:"mt-6"},P={class:"mt-6 flex justify-end"},z={__name:"DeleteUserForm",setup(T){const r=u(!1),l=u(null),e=_({password:""}),p=()=>{r.value=!0,h(()=>l.value.focus())},d=()=>{e.delete(route("profile.destroy"),{preserveScroll:!0,onSuccess:()=>n(),onError:()=>l.value.focus(),onFinish:()=>e.reset()})},n=()=>{r.value=!1,e.reset()};return(E,i)=>(y(),w("section",V,[B,o(m,{onClick:p},{default:a(()=>[c("Delete Account")]),_:1}),o(b,{show:r.value,onClose:n},{default:a(()=>[s("div",U,[$,A,s("div",N,[o(v,{for:"password",value:"Password",class:"sr-only"}),o(k,{id:"password",ref_key:"passwordInput",ref:l,modelValue:t(e).password,"onUpdate:modelValue":i[0]||(i[0]=f=>t(e).password=f),type:"password",class:"mt-1 block w-3/4",placeholder:"Password",onKeyup:x(d,["enter"])},null,8,["modelValue"]),o(D,{message:t(e).errors.password,class:"mt-2"},null,8,["message"])]),s("div",P,[o(C,{onClick:n},{default:a(()=>[c(" Cancel ")]),_:1}),o(m,{class:g(["ms-3",{"opacity-25":t(e).processing}]),disabled:t(e).processing,onClick:d},{default:a(()=>[c(" Delete Account ")]),_:1},8,["class","disabled"])])])]),_:1},8,["show"])]))}};export{z as default};
