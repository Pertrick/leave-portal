import { usePermission } from '@/composables/usePermission'

export const permission = {
  mounted(el, binding) {
    const { can, hasRole, hasAnyRole, hasAnyPermission } = usePermission()
    
    let hasAccess = false
    
    if (binding.arg === 'role') {
      hasAccess = hasRole(binding.value)
    } else if (binding.arg === 'any-role') {
      hasAccess = hasAnyRole(binding.value)
    } else if (binding.arg === 'any-permission') {
      hasAccess = hasAnyPermission(binding.value)
    } else {
      // Default to permission check
      hasAccess = can(binding.value)
    }
    
    if (!hasAccess) {
      el.style.display = 'none'
    }
  }
}

export const role = {
  mounted(el, binding) {
    const { hasRole } = usePermission()
    
    if (!hasRole(binding.value)) {
      el.style.display = 'none'
    }
  }
} 