<template>
  <li
    class="dropdown dropdown-submenu"
    :class="{
      'show': isOpen,
      'disabled': item.disabled,
      'sidebar-group-active active open': isActive,
      'openLeft': openChildDropdownOnLeft
    }"
    @mouseenter="() => updateGroupOpen(true)"
    @mouseleave="() => updateGroupOpen(false)"
  >
    <b-link
      class="dropdown-item"
      href="#"
      :class="{'dropdown-toggle': item.children}"
      @click="() => updateGroupOpen(!isOpen)"
    >
      <feather-icon :icon="item.icon || 'CircleIcon'" />
      <span class="menu-title">{{ item.title }}</span>
    </b-link>
    <ul
      ref="refChildDropdown"
      class="dropdown-menu"
    >
      <component
        :is="resolveNavItemComponent(child)"
        v-for="child in item.children"
        :key="child.header || child.title"
        ref="groupChild"
        :item="child"
      />
    </ul>
  </li>
</template>

<script>
import { BLink } from 'bootstrap-vue'
import { resolveHorizontalNavMenuItemComponent as resolveNavItemComponent } from '@core/layouts/utils'
import HorizontalNavMenuLink from '../horizontal-nav-menu-link/HorizontalNavMenuLink.vue'

// import { ref } from '@vue/composition-api'

// Composition Function
import useHorizontalNavMenuGroup from './useHorizontalNavMenuGroup'
import mixinHorizontalNavMenuGroup from './mixinHorizontalNavMenuGroup'

export default {
  name: 'HorizontalNavMenuGroup',
  components: {
    HorizontalNavMenuLink,
    BLink,
  },
  mixins: [mixinHorizontalNavMenuGroup],
  props: {
    item: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    const {
      refChildDropdown,
      isActive,
      isOpen,
      updateGroupOpen,
      updateIsActive,
      openChildDropdownOnLeft,
    } = useHorizontalNavMenuGroup(props.item)

    return {
      refChildDropdown,
      openChildDropdownOnLeft,
      resolveNavItemComponent,
      isOpen,
      isActive,
      updateGroupOpen,
      updateIsActive,
    }
  },
}
</script>

<style>

</style>
