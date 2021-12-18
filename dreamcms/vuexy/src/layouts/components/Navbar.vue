<template>
  <div class="navbar-container d-flex content align-items-center">

    <!-- Nav Menu Toggler -->
    <ul class="nav navbar-nav d-xl-none">
      <li class="nav-item">
        <b-link
          class="nav-link"
          @click="toggleVerticalMenuActive"
        >
          <feather-icon
            icon="MenuIcon"
            size="21"
          />
        </b-link>
      </li>
    </ul>

    <!-- Left Col -->
    <div class="bookmark-wrapper align-items-center flex-grow-1 d-none d-lg-flex">
      <dark-Toggler class="d-none d-lg-block" />
    </div>

    <b-navbar-nav class="nav align-items-center ml-auto">
      <b-nav-item-dropdown
        right
        toggle-class="d-flex align-items-center dropdown-user-link"
        class="dropdown-user"
      >
        <template #button-content>
          <div class="d-sm-flex d-none user-nav">
            <p class="user-name font-weight-bolder mb-0">
                {{ user.login }}
            </p>
            <span class="user-status" v-html="role"></span>
          </div>
          <b-avatar
            size="40"
            variant="light-primary"
            badge
            :src="getHeadUrl(user.uuid)"
            class="badge-minimal"
            badge-variant="success"
          />
        </template>

        <b-dropdown-item link-class="d-flex align-items-center" :href="'/user/' + user.login">
          <feather-icon
            size="16"
            icon="UserIcon"
            class="mr-50"
          />
          <span>Профиль</span>
        </b-dropdown-item>

        <b-dropdown-item link-class="d-flex align-items-center" href="/forum">
          <feather-icon
            size="16"
            icon="MessageSquareIcon"
            class="mr-50"
          />
          <span>Форум</span>
        </b-dropdown-item>

        <b-dropdown-divider />

        <b-dropdown-item link-class="d-flex align-items-center">
          <feather-icon
            size="16"
            icon="LogOutIcon"
            class="mr-50"
          />
          <span>Выход</span>
        </b-dropdown-item>
      </b-nav-item-dropdown>
    </b-navbar-nav>
  </div>
</template>

<script>
import {
  BLink, BNavbarNav, BNavItemDropdown, BDropdownItem, BDropdownDivider, BAvatar,
} from 'bootstrap-vue'
import DarkToggler from '@core/layouts/components/app-navbar/components/DarkToggler.vue'
import {mapGetters} from "vuex";

export default {
    computed:{
        ...mapGetters(['user', 'role'])
    },
  components: {
    BLink,
    BNavbarNav,
    BNavItemDropdown,
    BDropdownItem,
    BDropdownDivider,
    BAvatar,

    // Navbar Components
    DarkToggler,
  },
  props: {
    toggleVerticalMenuActive: {
      type: Function,
      default: () => {},
    },
  },
}
</script>
