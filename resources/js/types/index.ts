export interface NavigationItem {
    id: number;
    path: string;
    title: string;
    icon?: string;
    roles?: string[];
    children?: NavigationItem[];
    parent_id?: number;
    order?: number;
    is_active?: boolean;
} 