import { ref, computed } from '@vue/composition-api'
import { isNavGroupActive } from '@core/layouts/utils'

// ACL
import ability from '@/libs/acl/ability'

export default function useHorizontalNavMenuHeaderGroup(item) {
  // ------------------------------------------------
  // isOpen
  // ------------------------------------------------
  const isOpen = ref(false)

  const updateGroupOpen = val => {
    // eslint-disable-next-line no-use-before-define
    isOpen.value = val
  }

  // ------------------------------------------------
  // isActive
  // ------------------------------------------------
  const isActive = ref(false)

  const updateIsActive = () => {
    isActive.value = isNavGroupActive(item.children)
  }

  // ------------------------------------------------
  // hasAbilityToAccess
  // ------------------------------------------------

  // eslint-disable-next-line arrow-body-style
  const hasAbilityToAccess = computed(() => {
    return item.children.some(grpOrItem => {
      // If it have children => It's grp
      if (grpOrItem.children) {
        return grpOrItem.children.some(i => ability.can(i.action, i.resource))
      }

      // Else it's item
      return ability.can(grpOrItem.action, grpOrItem.resource)
    })
  })

  return {
    isOpen,
    isActive,
    updateGroupOpen,
    updateIsActive,

    // ACL
    hasAbilityToAccess,
  }
}
