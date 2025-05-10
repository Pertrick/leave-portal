import { type BreadcrumbItem } from '@/types';
import { usePage } from '@inertiajs/vue3';

interface BreadcrumbConfig {
    path: string;
    title: string;
    roles?: string[];
    children?: BreadcrumbConfig[];
}

const breadcrumbConfigs: BreadcrumbConfig[] = [
    {
        path: '/dashboard',
        title: 'Dashboard',
        roles: ['admin', 'manager', 'staff'],
    },
    {
        path: '/leave',
        title: 'Leave Management',
        roles: ['admin', 'manager', 'staff'],
        children: [
            {
                path: '/leave/apply',
                title: 'Apply Leave',
                roles: ['admin', 'manager', 'staff'],
            },
            {
                path: '/leave/history',
                title: 'Leave History',
                roles: ['admin', 'manager', 'staff'],
            },
            {
                path: '/leave/approvals',
                title: 'Leave Approvals',
                roles: ['admin', 'manager'],
            },
        ],
    },
    {
        path: '/staff',
        title: 'Staff Management',
        roles: ['admin'],
        children: [
            {
                path: '/staff/list',
                title: 'Staff List',
                roles: ['admin'],
            },
            {
                path: '/staff/register',
                title: 'Register Staff',
                roles: ['admin'],
            },
        ],
    },
    {
        path: '/departments',
        title: 'Departments',
        roles: ['admin'],
    },
    {
        path: '/reports',
        title: 'Reports',
        roles: ['admin', 'manager'],
        children: [
            {
                path: '/reports/leave',
                title: 'Leave Reports',
                roles: ['admin', 'manager'],
            },
            {
                path: '/reports/staff',
                title: 'Staff Reports',
                roles: ['admin'],
            },
        ],
    },
    {
        path: '/settings',
        title: 'Settings',
        roles: ['admin'],
    },
];

export class BreadcrumbService {
    private static getCurrentPath(): string {
        return window.location.pathname;
    }

    private static getUserRoles(): string[] {
        const page = usePage();
        return page.props.auth?.user?.roles || [];
    }

    private static findBreadcrumbConfig(path: string, configs: BreadcrumbConfig[]): BreadcrumbConfig | null {
        for (const config of configs) {
            if (config.path === path) {
                return config;
            }
            if (config.children) {
                const found = this.findBreadcrumbConfig(path, config.children);
                if (found) return found;
            }
        }
        return null;
    }

    private static buildBreadcrumbs(path: string, configs: BreadcrumbConfig[]): BreadcrumbItem[] {
        const userRoles = this.getUserRoles();
        const breadcrumbs: BreadcrumbItem[] = [];
        const pathParts = path.split('/').filter(Boolean);

        let currentPath = '';
        for (const part of pathParts) {
            currentPath += `/${part}`;
            const config = this.findBreadcrumbConfig(currentPath, configs);
            
            if (config && (!config.roles || config.roles.some(role => userRoles.includes(role)))) {
                breadcrumbs.push({
                    title: config.title,
                    href: config.path,
                });
            }
        }

        return breadcrumbs;
    }

    public static getBreadcrumbs(): BreadcrumbItem[] {
        const currentPath = this.getCurrentPath();
        return this.buildBreadcrumbs(currentPath, breadcrumbConfigs);
    }
} 