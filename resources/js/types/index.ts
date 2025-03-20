import type { LucideIcon } from 'lucide-vue-next';
import { PageProps } from '@inertiajs/core'
import type { InertiaForm } from '@inertiajs/vue3';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface SharedData extends PageProps {
    auth: Auth
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface FormField {
    name: string;
    component: 'TextField';
    required?: boolean;
    type?: 'text' | 'email' | 'password' | 'checkbox' | 'radio';
    label?: string;
    precognitive?: boolean;
    value?: string|number|boolean;
    class?: string;
}

export interface FormProps extends InertiaForm<any>{
    url: string
    method: 'post'|'patch'|'put'|'delete'
    fields: FormField[]
}

export interface PaginatedMeta {
    currentPage: number
    perPage: number
    total: number
    lastPage: number
    perPageItems: number[]
}

export interface TableHeaderOptions {
    sortable: boolean
    searchable: boolean
    toggleable: boolean
    headerAlignment: 'justify-start'|'justify-center'|'justify-end'
    alignment: 'justify-start'|'justify-center'|'justify-end'
    wrap: boolean
    truncate: number
    visible: boolean
}

export interface TableHeaderWithoutOptions {
    name: string
    header: string
    width: string,
}

export interface TableHeader extends TableHeaderWithoutOptions {
    options: TableHeaderOptions
}

export interface Paginated<T> {
    pageName: string
    data: T[]
    meta: PaginatedMeta
    headers: TableHeader[]
}
