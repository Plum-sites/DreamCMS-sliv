<template>
  <div v-if="loaded">
    <b-row>
      <b-col sm="12" md="4">
        <b-card>
          <b-card-title>
            Выбор игрока
          </b-card-title>

          <user-selector v-model="selected.user"></user-selector>
        </b-card>
      </b-col>
      <b-col sm="12" md="4">
        <b-card>
          <b-card-title>
            Выбор группы
          </b-card-title>

          <v-select v-model="selected.role" :options="roles" label="name" :reduce="item => item.id"></v-select>
        </b-card>
      </b-col>
      <b-col sm="12" md="4" v-if="loaded && data && (data.acluser || data.aclrole)">
        <b-card>
          <b-card-title v-if="data.acluser">
            Пользователь:
            <b-avatar :src="getHeadUrl(data.acluser.uuid)"></b-avatar>
            {{ data.acluser.login }}
          </b-card-title>

          <b-card-title v-if="data.aclrole">
            Роль: {{ data.aclrole.name }}
          </b-card-title>

          Не забудьте сохранить изменения!

          <hr>

          <b-button variant="danger" @click="clear">Очистить права</b-button>
          <b-button variant="success" @click="save">Сохранить</b-button>
        </b-card>
      </b-col>
    </b-row>

    <b-row v-if="loaded && data && data.common_map">
      <b-col sm="12">
        <b-card>
          <b-card-title>
            Редактирование
          </b-card-title>

          <b-tabs>
            <b-tab :title="category_key" :key="category_key" v-for="(category, category_key) in data.common_map">
              <b-card :key="subcat_key" v-for="(subcat, subcat_key) in category">
                <b-card-title>
                  {{ subcat.category }}
                </b-card-title>

                <table class="table b-table">
                  <tbody>
                  <tr>
                    <th>Наименование</th>
                    <th v-for="permission in subcat.permissions"></th>
                  </tr>

                  <tr v-if="subcat.child" v-for="(child, childkey) in subcat.child">
                    <td>{{ child.label }}</td>
                    <td v-for="(permission, permission_key) in subcat.permissions">
                      <br>
                      <b-form-checkbox v-b-tooltip.hover.top="subcat_key + '.' + childkey + '.' + permission_key" @click.right.stop="copy(subcat_key + '.' + childkey + '.' + permission_key)"
                                       :disabled="child.permissions instanceof Array ? !child.permissions.includes(permission_key) : !child.permissions.hasOwnProperty(permission_key)"
                                       v-model="edit.permissions[subcat_key + '.' + childkey + '.' + permission_key]">
                        {{ permission }}
                      </b-form-checkbox>
                    </td>
                  </tr>

                  <tr v-if="!subcat.child">
                    <td>Все</td>
                    <td v-for="(permission, permission_key) in subcat.permissions">
                      <br>
                      <b-form-checkbox v-model="edit.admin_permissions[subcat_key][permission_key]">{{ permission }}</b-form-checkbox>
                    </td>
                  </tr>
                  </tbody>
                </table>
              </b-card>
            </b-tab>

            <b-tab title="Доступ к ограниченным моделям">
              <b-card :key="model_class" :heading="model.category" v-for="(model, model_class) in data.models_map">
                <b-card-title>
                  {{ model.category }}
                </b-card-title>

                <table class="table b-table">
                  <tbody>
                  <tr>
                    <th>Наименование</th>
                    <th v-for="(permission, permission_key) in model.permissions"></th>
                  </tr>
                  <tr v-for="entity in model.entity">
                    <td>{{ entity[model.label] }}</td>
                    <td v-for="(permission, permission_key) in model.permissions">
                      <br>
                      <b-form-checkbox v-model="edit.model_permissions[model_class][permission_key][entity.id]">
                        {{ permission }}
                      </b-form-checkbox>
                    </td>
                  </tr>
                  </tbody>
                </table>
              </b-card>
            </b-tab>
          </b-tabs>
        </b-card>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import api from '../api';
import UserSelector from '../components/UserSelector';
import ItemSelector from "../components/ItemSelector";
import {
  BAvatar,
  BButton,
  BCard, BCardTitle,
  BCol,
  BForm,
  BFormCheckbox, BFormGroup,
  BFormInput,
  BRow,
  BSpinner,
  BTable,
  BTabs, BTab, VBTooltip
} from "bootstrap-vue";
import vSelect from "vue-select";
import flatPickr from "vue-flatpickr-component";
import AppCollapse from "@core/components/app-collapse/AppCollapse";
import AppCollapseItem from "@core/components/app-collapse/AppCollapseItem";

export default {
  name: "ACL",
  components: {
    UserSelector,
    ItemSelector,
    BCard,
    BButton,
    BAvatar,
    BRow,
    BCol,
    BFormInput,
    BFormCheckbox,
    BSpinner,
    BForm,
    BTable,
    BFormGroup,
    vSelect,
    flatPickr,
    AppCollapse,
    AppCollapseItem,
    BCardTitle,
    BTabs, BTab
  },
  directives: {
    'b-tooltip': VBTooltip
  },
  mounted() {
    this.loadData();
  },
  data() {
    return {
      loaded: false,

      selected: {
        user: null,
        role: null
      },

      roles: [],

      data: {
        acluser: false,
        aclrole: false,
        common_map: false,
        models_map: false
      },

      edit: {
        permissions: {},
        admin_permissions: {},
        model_permissions: {},
      }
    }
  },

  methods: {
    clear() {

    },
    copy(str) {
      alert(str);
    },
    save() {
      var arr = [];

      for (const [permission, value] of Object.entries(this.edit.permissions)) {
        if (value) arr.push({type: 'perm', perm: permission});
      }

      for (const [model_class, model] of Object.entries(this.edit.model_permissions)) {
        for (const [perm, permarr] of Object.entries(model)) {
          permarr.forEach((value, id) => {
            if (value) {
              arr.push({type: 'byid', model: model_class, perm: perm, id: id});
            }
          });
        }
      }

      for (const [model_class, model] of Object.entries(this.edit.admin_permissions)) {
        for (const [perm, value] of Object.entries(model)) {
          if (value) {
            arr.push({type: 'bymodel', model: model_class, perm: perm});
          }
        }
      }

      api.post("acl/save", {
        arr: arr,
        user: this.selected.user ? this.selected.user.id : null,
        role: this.selected.role
      })
          .then(response => {
            console.log(response);
          })
          .catch(error => {
            console.log(error);
          });
    },
    loadData() {
      this.loaded = false;
      api.post("acl/load", {user: this.selected.user ? this.selected.user.id : null, role: this.selected.role})
          .then(response => {
            this.loaded = false;

            this.edit.permissions = {};
            this.edit.admin_permissions = {};
            this.edit.model_permissions = {};

            if (response.data.roles) {
              this.roles = response.data.roles;
            } else {
              this.data.acluser = response.data.acluser;
              this.data.aclrole = response.data.aclrole;
              this.data.common_map = response.data.common_map;
              this.data.models_map = response.data.models_map;

              for (const [category_key, category] of Object.entries(response.data.common_map)) {
                for (const [model_key, model] of Object.entries(category)) {
                  if (model.child) {
                    for (const [child_key, child] of Object.entries(model.child)) {
                      if (child.permissions instanceof Array) {
                        child.permissions.forEach((permission_key) => {
                          var key = model_key + '.' + child_key + '.' + permission_key;
                          this.$set(this.edit.permissions, key, response.data.perms.includes(key));
                        });
                      } else {
                        for (const [permission_key, permission] of Object.entries(child.permissions)) {
                          var key = model_key + '.' + child_key + '.' + permission_key;
                          this.$set(this.edit.permissions, key, response.data.perms.includes(key));
                        }
                      }
                    }
                  } else {
                    this.$set(this.edit.admin_permissions, model_key, {});

                    for (const [permission_key, permission] of Object.entries(model.permissions)) {
                      var has = false;

                      try {
                        has = response.data.models_perms[model_key]['global'] instanceof Array ? response.data.models_perms[model_key]['global'].includes(permission_key) : false;
                      } catch (e) {
                      }

                      this.$set(this.edit.admin_permissions[model_key], permission_key, has);
                    }
                  }
                }
              }

              for (const [model_class, model] of Object.entries(response.data.models_map)) {
                this.$set(this.edit.model_permissions, model_class, {});

                for (const [permission_key, permission] of Object.entries(model.permissions)) {
                  this.$set(this.edit.model_permissions[model_class], permission_key, []);

                  model.entity.forEach((entity) => {
                    var has = false;

                    try {
                      has = response.data.models_perms[model_class][entity.id] instanceof Array ? response.data.models_perms[model_class][entity.id].includes(permission_key) : false;
                    } catch (e) {
                    }

                    this.$set(this.edit.model_permissions[model_class][permission_key], entity.id, has);
                  });
                }
              }
            }

            this.loaded = true;
            console.log(response);
          })
          .catch(error => {
            console.log(error);
          });
    },
  },

  watch: {
    'selected.user': function (val) {
      this.selected.role = null;
      this.loadData();
    },
    'selected.role': function (val) {
      this.selected.user = null;
      this.loadData();
    }
  }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-flatpicker.scss';
@import '@core/scss/vue/libs/vue-select.scss';
</style>