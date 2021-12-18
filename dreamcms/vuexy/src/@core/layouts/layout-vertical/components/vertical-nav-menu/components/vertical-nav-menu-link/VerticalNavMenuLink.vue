<template>
  <li
    class="nav-item"
    :class="{
      'active': isActive,
      'disabled': item.disabled
    }"
  >
    <b-link
      v-bind="linkProps"
      class="d-flex align-items-center"
    >
      <feather-icon :icon="item.icon || 'CircleIcon'" />
      <span class="menu-title text-truncate">{{ item.title }}</span>
      <b-badge
        v-if="item.tag"
        pill
        :variant="item.tagVariant || 'primary'"
        class="mr-1 ml-auto"
      >
        {{ item.tag }}
      </b-badge>
    </b-link>
  </li>
</template>

<script>
// import { ref } from '@vue/composition-api'
import { BLink, BBadge } from 'bootstrap-vue'
import useVerticalNavMenuLink from './useVerticalNavMenuLink'
import mixinVerticalNavMenuLink from './mixinVerticalNavMenuLink'

export default {
  components: {
    BLink,
    BBadge,
  },
  mixins: [mixinVerticalNavMenuLink],
  props: {
    item: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    const { isActive, linkProps, updateIsActive } = useVerticalNavMenuLink(props.item)

    return {
      isActive,
      linkProps,
      updateIsActive,
    }
  },

}
</script>
