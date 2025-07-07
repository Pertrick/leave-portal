import { usePage } from '@inertiajs/vue3'

export function usePermission() {
  const page = usePage()

  const can = (permission) => {
    const user = page.props.auth?.user
    if (!user) return false
    
    // Check direct permissions
    if (user.permissions?.includes(permission)) return true
    
    // Check role-based permissions (admin has all permissions)
    if (user.roles?.includes('admin')) return true
    
    return false
  }

  const hasRole = (role) => {
    const user = page.props.auth?.user
    if (!user) return false
    
    return user.roles?.includes(role) || false
  }

  const hasAnyRole = (roles) => {
    const user = page.props.auth?.user
    if (!user) return false
    
    if (typeof roles === 'string') {
      roles = roles.split('|')
    }
    
    return roles.some(role => user.roles?.includes(role)) || false
  }

  const hasAnyPermission = (permissions) => {
    const user = page.props.auth?.user
    if (!user) return false
    
    if (typeof permissions === 'string') {
      permissions = permissions.split('|')
    }
    
    // Admin has all permissions
    if (user.roles?.includes('admin')) return true
    
    return permissions.some(permission => user.permissions?.includes(permission)) || false
  }

  const getUserRoles = () => {
    return page.props.auth?.user?.roles || []
  }

  const getUserPermissions = () => {
    return page.props.auth?.user?.permissions || []
  }

  return {
    can,
    hasRole,
    hasAnyRole,
    hasAnyPermission,
    getUserRoles,
    getUserPermissions
  }
} 